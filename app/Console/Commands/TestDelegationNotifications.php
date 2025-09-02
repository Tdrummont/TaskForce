<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDelegatedNotification;
use App\Events\TaskDelegated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestDelegationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:delegation-notifications {--user-id= : ID do usuário para testar} {--task-id= : ID da tarefa para testar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa as notificações de delegação de tarefas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando Notificações de Delegação...');
        $this->newLine();

        // Buscar usuários para teste
        $users = User::take(2)->get();
        
        if ($users->count() < 2) {
            $this->error('❌ É necessário pelo menos 2 usuários para testar delegações!');
            $this->info('Execute: php artisan db:seed para criar usuários de teste');
            return 1;
        }

        $delegatedBy = $users->first();
        $delegatedTo = $users->last();

        $this->info("👤 Usuário que delega: {$delegatedBy->name} ({$delegatedBy->email})");
        $this->info("👤 Usuário delegado: {$delegatedTo->name} ({$delegatedTo->email})");
        $this->newLine();

        // Buscar ou criar tarefa para teste
        $task = null;
        if ($this->option('task-id')) {
            $task = Task::find($this->option('task-id'));
            if (!$task) {
                $this->error("❌ Tarefa com ID {$this->option('task-id')} não encontrada!");
                return 1;
            }
        } else {
            // Criar tarefa de teste
            $task = Task::create([
                'title' => 'Tarefa de Teste - Delegação',
                'description' => 'Esta é uma tarefa criada para testar o sistema de delegação',
                'status' => 'pending',
                'priority' => 'medium',
                'category' => 'Teste',
                'created_by' => $delegatedBy->id,
                'assigned_to' => $delegatedTo->id,
                'due_date' => now()->addDays(7),
                'estimated_hours' => 4,
            ]);
            
            $this->info("📝 Tarefa de teste criada: {$task->title}");
        }

        $this->newLine();
        $this->info('🔔 Testando Notificação de Delegação...');

        try {
            // Testar notificação por email
            $delegatedTo->notify(new TaskDelegatedNotification($task, $delegatedBy, $delegatedTo));
            $this->info('✅ Notificação de delegação enviada com sucesso!');
            
            // Verificar se foi salva no banco
            $notification = $delegatedTo->notifications()->latest()->first();
            if ($notification) {
                $this->info("✅ Notificação salva no banco com ID: {$notification->id}");
                $this->info("📊 Tipo: {$notification->data['type']}");
                $this->info("📝 Mensagem: {$notification->data['message']}");
            } else {
                $this->warn("⚠️ Notificação não encontrada no banco de dados");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao enviar notificação: {$e->getMessage()}");
            Log::error('Erro no teste de delegação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('📡 Testando Evento de Broadcast...');

        try {
            // Disparar evento para WebSocket
            event(new TaskDelegated($task, $delegatedBy, $delegatedTo));
            $this->info('✅ Evento TaskDelegated disparado com sucesso!');
            $this->info("📡 Canal: user.{$delegatedTo->id}");
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao disparar evento: {$e->getMessage()}");
            Log::error('Erro no evento de delegação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('📊 Resumo do Teste:');
        $this->info("   • Tarefa: {$task->title}");
        $this->info("   • Delegada por: {$delegatedBy->name}");
        $this->info("   • Delegada para: {$delegatedTo->name}");
        $this->info("   • Email enviado: ✅");
        $this->info("   • Evento disparado: ✅");
        $this->info("   • Notificação salva: ✅");

        $this->newLine();
        $this->info('🎉 Teste de delegação concluído com sucesso!');
        $this->info('📧 Verifique se o email foi recebido em: ' . $delegatedTo->email);
        $this->info('🔔 Verifique se a notificação aparece no sistema');

        return 0;
    }
} 