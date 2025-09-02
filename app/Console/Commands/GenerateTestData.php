<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data {--user-id= : Generate data for specific user} {--days=30 : Number of days to generate data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera dados de teste para relatórios e estatísticas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $days = (int) $this->option('days');

        $this->info('🔄 Gerando dados de teste para relatórios...');

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("❌ Usuário com ID {$userId} não encontrado.");
                return 1;
            }
            $users = collect([$user]);
        } else {
            $users = User::all();
        }

        $totalTasks = 0;
        $totalActivities = 0;

        foreach ($users as $user) {
            $this->info("\n👤 Gerando dados para: {$user->name} (ID: {$user->id})");

            // Gerar tarefas nos últimos X dias
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                
                // Gerar 1-5 tarefas por dia
                $tasksPerDay = rand(1, 5);
                
                for ($j = 0; $j < $tasksPerDay; $j++) {
                    $task = $this->createRandomTask($user, $date);
                    $totalTasks++;
                    
                    // Gerar atividades para a tarefa
                    $activities = $this->createRandomActivities($user, $task, $date);
                    $totalActivities += count($activities);
                }
            }

            $this->line("   ✅ {$totalTasks} tarefas criadas");
            $this->line("   ✅ {$totalActivities} atividades registradas");
        }

        $this->newLine();
        $this->info('📊 Resumo da geração de dados:');
        $this->line("   • Total de tarefas geradas: {$totalTasks}");
        $this->line("   • Total de atividades registradas: {$totalActivities}");
        $this->line("   • Período: últimos {$days} dias");

        $this->info('✅ Dados de teste gerados com sucesso!');
        $this->line('Acesse /reports para visualizar os relatórios.');

        return 0;
    }

    /**
     * Criar uma tarefa aleatória
     */
    private function createRandomTask(User $user, Carbon $date)
    {
        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high'];
        
        $titles = [
            'Implementar funcionalidade de login',
            'Corrigir bug no formulário',
            'Atualizar documentação',
            'Revisar código de segurança',
            'Otimizar performance',
            'Adicionar testes unitários',
            'Refatorar módulo de usuários',
            'Implementar cache',
            'Corrigir responsividade',
            'Adicionar validações'
        ];

        $descriptions = [
            'Implementar sistema de autenticação seguro',
            'Corrigir problema de validação no frontend',
            'Atualizar documentação da API',
            'Revisar vulnerabilidades de segurança',
            'Otimizar consultas ao banco de dados',
            'Criar testes para garantir qualidade',
            'Melhorar estrutura do código',
            'Implementar cache Redis',
            'Corrigir layout em dispositivos móveis',
            'Adicionar validações de entrada'
        ];

        $status = $statuses[array_rand($statuses)];
        $priority = $priorities[array_rand($priorities)];
        
        // Se a tarefa está completa, definir data de conclusão
        $completedAt = null;
        if ($status === 'completed') {
            $completedAt = $date->copy()->addDays(rand(1, 7));
        }

        $task = Task::create([
            'title' => $titles[array_rand($titles)],
            'description' => $descriptions[array_rand($descriptions)],
            'status' => $status,
            'priority' => $priority,
            'due_date' => $date->copy()->addDays(rand(1, 14)),
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'created_at' => $date,
            'updated_at' => $completedAt ?? $date
        ]);

        return $task;
    }

    /**
     * Criar atividades aleatórias para uma tarefa
     */
    private function createRandomActivities(User $user, Task $task, Carbon $date)
    {
        $activities = [];

        // Atividade de criação
        $activities[] = ActivityLog::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'action' => 'created',
            'description' => 'Tarefa criada',
            'created_at' => $date,
            'updated_at' => $date
        ]);

        // Se a tarefa não está pendente, adicionar atividades de mudança de status
        if ($task->status !== 'pending') {
            $activities[] = ActivityLog::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'action' => 'status_changed',
                'description' => 'Status alterado para ' . $this->getStatusLabel($task->status),
                'old_values' => ['status' => 'pending'],
                'new_values' => ['status' => $task->status],
                'created_at' => $date->copy()->addHours(rand(1, 8)),
                'updated_at' => $date->copy()->addHours(rand(1, 8))
            ]);
        }

        // Se a tarefa está completa, adicionar atividade de conclusão
        if ($task->status === 'completed') {
            $activities[] = ActivityLog::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'action' => 'completed',
                'description' => 'Tarefa concluída',
                'old_values' => ['status' => 'in_progress'],
                'new_values' => ['status' => 'completed'],
                'created_at' => $task->updated_at,
                'updated_at' => $task->updated_at
            ]);
        }

        // Adicionar algumas atualizações aleatórias
        if (rand(1, 3) === 1) {
            $activities[] = ActivityLog::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'action' => 'updated',
                'description' => 'Tarefa atualizada',
                'created_at' => $date->copy()->addHours(rand(2, 12)),
                'updated_at' => $date->copy()->addHours(rand(2, 12))
            ]);
        }

        return $activities;
    }

    /**
     * Retorna o label do status
     */
    private function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluída',
            default => $status
        };
    }
} 