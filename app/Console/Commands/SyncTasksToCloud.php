<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SyncTasksToCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:sync-cloud {--user-id= : Sync specific user tasks} {--dry-run : Show what would be synced without actually syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza tarefas dos usuários com a nuvem (simulado)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $dryRun = $this->option('dry-run');

        $this->info('🔄 Iniciando sincronização de tarefas com a nuvem...');

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
        $syncedTasks = 0;
        $errors = 0;

        foreach ($users as $user) {
            $this->info("\n👤 Sincronizando tarefas do usuário: {$user->name} (ID: {$user->id})");

            $tasks = Task::where('created_by', $user->id)
                ->orWhere('assigned_to', $user->id)
                ->with(['creator', 'assignee'])
                ->get();

            $totalTasks += $tasks->count();

            if ($tasks->isEmpty()) {
                $this->warn("   ⚠️  Nenhuma tarefa encontrada para o usuário {$user->name}");
                continue;
            }

            foreach ($tasks as $task) {
                try {
                    $taskData = [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'due_date' => $task->due_date ? $task->due_date->toISOString() : null,
                        'created_by' => $task->created_by,
                        'assigned_to' => $task->assigned_to,
                        'created_at' => $task->created_at->toISOString(),
                        'updated_at' => $task->updated_at->toISOString(),
                        'user_id' => $user->id,
                        'sync_timestamp' => now()->toISOString()
                    ];

                    if (!$dryRun) {
                        // Simular upload para a nuvem
                        $this->uploadToCloud($taskData, $user->id);
                        
                        // Salvar localmente como backup
                        $this->saveLocalBackup($taskData, $user->id);
                    }

                    $syncedTasks++;
                    $this->line("   ✅ Tarefa '{$task->title}' sincronizada");

                } catch (\Exception $e) {
                    $errors++;
                    $this->error("   ❌ Erro ao sincronizar tarefa '{$task->title}': {$e->getMessage()}");
                    Log::error("Erro na sincronização de tarefa", [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->newLine();
        $this->info('📊 Resumo da sincronização:');
        $this->line("   • Total de tarefas processadas: {$totalTasks}");
        $this->line("   • Tarefas sincronizadas: {$syncedTasks}");
        $this->line("   • Erros: {$errors}");

        if ($dryRun) {
            $this->warn('   ⚠️  Modo dry-run ativado - nenhuma sincronização real foi realizada');
        } else {
            $this->info('   ✅ Sincronização concluída com sucesso!');
        }

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Simula upload para a nuvem
     */
    private function uploadToCloud(array $taskData, int $userId): void
    {
        // Simular delay de rede
        usleep(rand(100000, 500000)); // 0.1 a 0.5 segundos

        // Simular falha ocasional (5% de chance)
        if (rand(1, 100) <= 5) {
            throw new \Exception('Erro de conexão com a nuvem');
        }

        // Salvar em arquivo local para simular nuvem
        $cloudPath = "cloud/tasks/user_{$userId}/task_{$taskData['id']}.json";
        Storage::put($cloudPath, json_encode($taskData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Salva backup local
     */
    private function saveLocalBackup(array $taskData, int $userId): void
    {
        $backupPath = "backups/sync/user_{$userId}/" . date('Y-m-d_H-i-s') . ".json";
        Storage::put($backupPath, json_encode($taskData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
} 