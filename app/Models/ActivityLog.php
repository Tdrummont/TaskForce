<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes para filtros
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    // Métodos auxiliares
    public function getActionLabel()
    {
        return match($this->action) {
            'created' => 'Criada',
            'updated' => 'Atualizada',
            'deleted' => 'Excluída',
            'status_changed' => 'Status Alterado',
            'priority_changed' => 'Prioridade Alterada',
            'assigned' => 'Atribuída',
            'completed' => 'Concluída',
            default => ucfirst($this->action)
        };
    }

    public function getActionIcon()
    {
        return match($this->action) {
            'created' => 'plus-circle',
            'updated' => 'edit',
            'deleted' => 'trash',
            'status_changed' => 'refresh',
            'priority_changed' => 'flag',
            'assigned' => 'user-plus',
            'completed' => 'check-circle',
            default => 'info-circle'
        };
    }

    public function getActionColor()
    {
        return match($this->action) {
            'created' => 'text-green-600',
            'updated' => 'text-blue-600',
            'deleted' => 'text-red-600',
            'status_changed' => 'text-yellow-600',
            'priority_changed' => 'text-purple-600',
            'assigned' => 'text-indigo-600',
            'completed' => 'text-green-600',
            default => 'text-gray-600'
        };
    }

    public function getFormattedDescription()
    {
        $description = $this->description ?? '';
        
        // Verificar se old_values e new_values existem e são válidos
        if (empty($this->old_values) || empty($this->new_values)) {
            return $description ?: 'Atividade registrada';
        }
        
        try {
            $changes = [];
            
            // Os casts já convertem automaticamente JSON para array
            $oldValues = $this->old_values;
            $newValues = $this->new_values;
            
            // Verificar se os valores são arrays válidos
            if (is_array($newValues) && is_array($oldValues) && !empty($newValues)) {
                foreach ($newValues as $field => $newValue) {
                    // Pular campos vazios ou nulos
                    if (empty($field) || is_null($field) || !is_string($field)) {
                        continue;
                    }
                    
                    $oldValue = $oldValues[$field] ?? null;
                    if ($oldValue !== $newValue) {
                        try {
                            $changes[] = $this->formatFieldChange($field, $oldValue, $newValue);
                        } catch (\Exception $e) {
                            // Log do erro e continuar
                            Log::warning("Erro ao formatar mudança de campo: {$field}", [
                                'old_value' => $oldValue,
                                'new_value' => $newValue,
                                'error' => $e->getMessage()
                            ]);
                            $changes[] = "{$field}: Alterado";
                        }
                    }
                }
            }
            
            if (!empty($changes)) {
                $description .= ': ' . implode(', ', array_filter($changes));
            }
        } catch (\Exception $e) {
            // Log do erro geral e retornar descrição básica
            Log::error("Erro ao formatar descrição de atividade", [
                'activity_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            
            return $description ?: 'Atividade registrada';
        }
        
        return $description ?: 'Atividade registrada';
    }

    private function formatFieldChange($field, $oldValue, $newValue)
    {
        // Validar campo
        if (empty($field) || !is_string($field)) {
            return 'Campo: Alterado';
        }
        
        $fieldLabels = [
            'title' => 'Título',
            'description' => 'Descrição',
            'status' => 'Status',
            'priority' => 'Prioridade',
            'due_date' => 'Data de Vencimento',
            'assigned_to' => 'Atribuído a'
        ];

        $fieldLabel = $fieldLabels[$field] ?? $field;

        try {
            if ($field === 'status') {
                $oldLabel = $this->getStatusLabel($oldValue);
                $newLabel = $this->getStatusLabel($newValue);
                return "{$fieldLabel}: {$oldLabel} → {$newLabel}";
            }

            if ($field === 'priority') {
                $oldLabel = $this->getPriorityLabel($oldValue);
                $newLabel = $this->getPriorityLabel($newValue);
                return "{$fieldLabel}: {$oldLabel} → {$newLabel}";
            }

            if ($field === 'due_date') {
                $oldDate = $oldValue ? Carbon::parse($oldValue)->format('d/m/Y') : 'Nenhuma';
                $newDate = $newValue ? Carbon::parse($newValue)->format('d/m/Y') : 'Nenhuma';
                return "{$fieldLabel}: {$oldDate} → {$newDate}";
            }

            // Tratar arrays e outros tipos de dados
            $oldValueStr = $this->formatValue($oldValue);
            $newValueStr = $this->formatValue($newValue);
            
            return "{$fieldLabel}: {$oldValueStr} → {$newValueStr}";
        } catch (\Exception $e) {
            // Log do erro e retornar mensagem genérica
            Log::warning("Erro ao formatar mudança de campo específico: {$field}", [
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'error' => $e->getMessage()
            ]);
            
            return "{$fieldLabel}: Alterado";
        }
    }

    private function formatValue($value, $depth = 0)
    {
        try {
            // Evitar recursão infinita
            if ($depth > 3) {
                return '...';
            }
            
            if (is_array($value)) {
                // Se for um array simples, tentar converter para string
                if (empty($value)) {
                    return 'Vazio';
                }
                
                // Se for um array associativo, converter para JSON
                if (array_keys($value) !== range(0, count($value) - 1)) {
                    return json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                
                // Se for um array indexado, converter para string separada por vírgula
                $formattedValues = [];
                foreach ($value as $item) {
                    $formattedValues[] = $this->formatValue($item, $depth + 1);
                }
                return implode(', ', $formattedValues);
            }
            
            if (is_null($value)) {
                return 'Nenhum';
            }
            
            if (is_bool($value)) {
                return $value ? 'Sim' : 'Não';
            }
            
            if (is_object($value)) {
                return method_exists($value, '__toString') ? (string) $value : get_class($value);
            }
            
            return (string) $value;
        } catch (\Exception $e) {
            // Log do erro e retornar valor genérico
            Log::warning("Erro ao formatar valor", [
                'value' => $value,
                'type' => gettype($value),
                'error' => $e->getMessage()
            ]);
            
            return 'Erro ao formatar';
        }
    }

    private function getStatusLabel($status)
    {
        if (is_array($status)) {
            return 'Array';
        }
        
        if (is_null($status)) {
            return 'Nenhum';
        }
        
        return match((string) $status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluída',
            default => (string) $status
        };
    }

    private function getPriorityLabel($priority)
    {
        if (is_array($priority)) {
            return 'Array';
        }
        
        if (is_null($priority)) {
            return 'Nenhum';
        }
        
        return match((string) $priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => (string) $priority
        };
    }
} 