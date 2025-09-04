<?php

namespace App\Console\Commands;

use App\Events\TaskAssigned;
use App\Events\TaskDelegated;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class TestPusherNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pusher {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa notificações em tempo real via Pusher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // Listar usuários disponíveis
            $users = User::select('id', 'name', 'email')->get();
            $this->info('Usuários disponíveis:');
            foreach ($users as $user) {
                $this->line("ID: {$user->id} - {$user->name} ({$user->email})");
            }
            $userId = $this->ask('Digite o ID do usuário para testar');
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuário com ID {$userId} não encontrado!");
            return 1;
        }

        $this->info("Testando notificações para: {$user->name} ({$user->email})");

        // Criar uma tarefa de teste
        $task = Task::create([
            'title' => 'Tarefa de Teste - ' . now()->format('H:i:s'),
            'description' => 'Esta é uma tarefa criada para testar notificações em tempo real.',
            'status' => 'pending',
            'priority' => 'medium',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
        ]);

        $this->info("Tarefa criada: {$task->title}");

        // Disparar evento de tarefa atribuída
        event(new TaskAssigned($task, $user, $user));
        $this->info('✅ Evento TaskAssigned disparado');

        // Aguardar um pouco
        sleep(2);

        // Disparar evento de tarefa delegada
        event(new TaskDelegated($task, $user, $user));
        $this->info('✅ Evento TaskDelegated disparado');

        $this->info("\n🎉 Teste concluído! Verifique se as notificações apareceram no frontend.");
        $this->info("Canal: private-user.{$user->id}");
        $this->info("Eventos: task.assigned, task.delegated");

        return 0;
    }
}