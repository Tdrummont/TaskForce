<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use \Exception;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            try {
                $model->logActivity('created', 'Tarefa criada');
            } catch (\Exception $e) {
                // Log do erro mas não interromper a funcionalidade
                Log::warning('Erro ao logar criação da tarefa: ' . $e->getMessage(), [
                    'task_id' => $model->id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        });

        static::updated(function ($model) {
            try {
                // Obter valores originais e atuais de forma segura
                $originalValues = $model->getOriginal();
                $currentValues = $model->getAttributes();
                
                // Filtrar valores sensíveis e garantir que sejam arrays válidos
                $originalValues = is_array($originalValues) ? $originalValues : [];
                $currentValues = is_array($currentValues) ? $currentValues : [];
                
                // Verificar se há mudanças reais antes de logar
                $changes = array_diff_assoc($currentValues, $originalValues);
                if (!empty($changes)) {
                    $model->logActivity('updated', 'Tarefa atualizada', $originalValues, $currentValues);
                }
            } catch (\Exception $e) {
                // Log do erro mas não interromper a funcionalidade
                Log::warning('Erro ao logar atualização da tarefa: ' . $e->getMessage(), [
                    'task_id' => $model->id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        });

        static::deleting(function ($model) {
            try {
                // Capturar o ID da tarefa ANTES da exclusão
                $taskId = $model->id;
                $userId = Auth::user()->id ?? null;
                
                if ($userId) {
                    // Obter atributos de forma segura
                    $attributes = $model->getAttributes();
                    $oldValues = is_array($attributes) ? $attributes : [];
                    
                    ActivityLog::create([
                        'user_id' => $userId,
                        'task_id' => $taskId,
                        'action' => 'deleted',
                        'description' => 'Tarefa excluída',
                        'old_values' => $oldValues,
                        'new_values' => null,
                        'ip_address' => Request::ip(),
                        'user_agent' => Request::userAgent()
                    ]);
                }
            } catch (\Exception $e) {
                // Log do erro mas não interromper a exclusão
                Log::warning('Erro ao logar exclusão da tarefa: ' . $e->getMessage(), [
                    'task_id' => $model->id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /**
     * Log an activity
     */
    public function logActivity($action, $description, $oldValues = null, $newValues = null)
    {
        try {
            if (!Auth::check()) {
                return;
            }

            $userId = Auth::user()->id;
            $taskId = $this->id ?? null;

            // Filtrar valores sensíveis e garantir que sejam arrays
            $oldValues = $this->filterSensitiveData($oldValues);
            $newValues = $this->filterSensitiveData($newValues);

            // Garantir que ambos sejam arrays válidos
            $oldValues = is_array($oldValues) ? $oldValues : [];
            $newValues = is_array($newValues) ? $newValues : [];

            // Detectar mudanças específicas
            try {
                $changes = array_diff_assoc($newValues, $oldValues);
                
                if (isset($changes['status'])) {
                    $this->createSpecificLog($userId, $taskId, 'status_changed', 'Status alterado', $oldValues, $newValues);
                }
                
                if (isset($changes['priority'])) {
                    $this->createSpecificLog($userId, $taskId, 'priority_changed', 'Prioridade alterada', $oldValues, $newValues);
                }
                
                if (isset($changes['assigned_to'])) {
                    $this->createSpecificLog($userId, $taskId, 'assigned', 'Tarefa atribuída', $oldValues, $newValues);
                }
                
                if (isset($changes['status']) && $changes['status'] === 'completed') {
                    $this->createSpecificLog($userId, $taskId, 'completed', 'Tarefa concluída', $oldValues, $newValues);
                }
            } catch (\Exception $e) {
                // Log do erro mas não interromper a funcionalidade
                Log::warning('Erro ao detectar mudanças na atividade: ' . $e->getMessage(), [
                    'task_id' => $taskId,
                    'old_values' => $oldValues,
                    'new_values' => $newValues
                ]);
            }

            // Log geral
            $this->createSpecificLog($userId, $taskId, $action, $description, $oldValues, $newValues);
            
        } catch (\Exception $e) {
            // Log do erro geral mas não interromper a funcionalidade
            Log::warning('Erro geral ao logar atividade: ' . $e->getMessage(), [
                'action' => $action,
                'description' => $description,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a specific activity log entry
     */
    private function createSpecificLog($userId, $taskId, $action, $description, $oldValues = null, $newValues = null)
    {
        try {
            // Garantir que os valores sejam arrays válidos ou null
            $oldValuesData = is_array($oldValues) ? $oldValues : null;
            $newValuesData = is_array($newValues) ? $newValues : null;
            
            // Validar dados antes de criar o log
            if (!$userId || !$taskId) {
                Log::warning('Dados inválidos para criar log de atividade', [
                    'user_id' => $userId,
                    'task_id' => $taskId,
                    'action' => $action
                ]);
                return;
            }
            
            ActivityLog::create([
                'user_id' => $userId,
                'task_id' => $taskId,
                'action' => $action,
                'description' => $description,
                'old_values' => $oldValuesData,
                'new_values' => $newValuesData,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
            
        } catch (\Exception $e) {
            // Log do erro mas não interromper a funcionalidade
            Log::warning('Erro ao criar log de atividade específico: ' . $e->getMessage(), [
                'user_id' => $userId,
                'task_id' => $taskId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Filter sensitive data from logs
     */
    private function filterSensitiveData($data)
    {
        try {
            // Se não há dados, retornar array vazio
            if (!$data) {
                return [];
            }
            
            // Se não é array, tentar converter ou retornar array vazio
            if (!is_array($data)) {
                // Se for string, tentar decodificar JSON
                if (is_string($data)) {
                    $decoded = json_decode($data, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $data = $decoded;
                    } else {
                        return [];
                    }
                } else {
                    return [];
                }
            }

            $sensitiveFields = ['password', 'remember_token', 'email_verified_at'];
            
            $filtered = array_diff_key($data, array_flip($sensitiveFields));
            
            // Sempre retornar array, mesmo que vazio
            return is_array($filtered) ? $filtered : [];
            
        } catch (\Exception $e) {
            // Log do erro e retornar array vazio
            Log::warning('Erro ao filtrar dados sensíveis: ' . $e->getMessage(), [
                'data_type' => gettype($data),
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get activity logs for this model
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'task_id');
    }

    /**
     * Get recent activity logs
     */
    public function getRecentActivityLogs($limit = 10)
    {
        return $this->activityLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
} 