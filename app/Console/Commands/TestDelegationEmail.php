<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDelegatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestDelegationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:delegation-email {--user-email=} {--task-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de email de delegação de tarefa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->option('user-email') ?? 'gabyribeiro001@gmail.com';
        $taskId = $this->option('task-id');

        $this->info("🧪 Testando envio de email de delegação...");
        $this->info("📧 Email destinatário: {$userEmail}");

        // Buscar usuário destinatário
        $delegatedTo = User::where('email', $userEmail)->first();
        if (!$delegatedTo) {
            $this->error("❌ Usuário com email {$userEmail} não encontrado!");
            return 1;
        }

        $this->info("👤 Usuário encontrado: {$delegatedTo->name} (ID: {$delegatedTo->id})");

        // Buscar tarefa
        if ($taskId) {
            $task = Task::find($taskId);
            if (!$task) {
                $this->error("❌ Tarefa com ID {$taskId} não encontrada!");
                return 1;
            }
        } else {
            // Buscar uma tarefa recente
            $task = Task::latest()->first();
            if (!$task) {
                $this->error("❌ Nenhuma tarefa encontrada no sistema!");
                return 1;
            }
        }

        $this->info("📋 Tarefa selecionada: {$task->title} (ID: {$task->id})");

        // Buscar usuário que está delegando (criador da tarefa)
        $delegatedBy = User::find($task->created_by);
        if (!$delegatedBy) {
            $this->error("❌ Usuário criador da tarefa não encontrado!");
            return 1;
        }

        $this->info("👤 Usuário delegando: {$delegatedBy->name} (ID: {$delegatedBy->id})");

        try {
            $this->info("📤 Enviando notificação de delegação...");
            
            // Enviar notificação
            $delegatedTo->notify(new TaskDelegatedNotification($task, $delegatedBy, $delegatedTo));
            
            $this->info("✅ Notificação de delegação enviada com sucesso!");
            $this->info("📧 Email deve chegar em alguns segundos para: {$delegatedTo->email}");
            
            Log::info('🧪 Teste de email de delegação executado via comando', [
                'task_id' => $task->id,
                'delegated_by' => $delegatedBy->id,
                'delegated_to' => $delegatedTo->id,
                'delegated_to_email' => $delegatedTo->email
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao enviar notificação: " . $e->getMessage());
            
            Log::error('❌ Erro no teste de email de delegação', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
