<?php

namespace App\Console\Commands;

use App\Mail\TaskAssignedMail;
use App\Mail\UserLoginMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {user_id?} {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de emails (login e delegação de tarefas)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $type = $this->option('type');
        
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

        $this->info("Testando envio de emails para: {$user->name} ({$user->email})");

        try {
            // Testar email de login
            if ($type === 'all' || $type === 'login') {
                $this->info("\n📧 Testando email de login...");
                
                $loginMail = new UserLoginMail(
                    $user, 
                    '192.168.1.100', 
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                );
                
                Mail::to($user->email)->send($loginMail);
                $this->info('✅ Email de login enviado com sucesso!');
            }

            // Testar email de delegação de tarefa
            if ($type === 'all' || $type === 'task') {
                $this->info("\n📧 Testando email de delegação de tarefa...");
                
                // Criar uma tarefa de teste
                $task = Task::create([
                    'title' => 'Tarefa de Teste - Email - ' . now()->format('H:i:s'),
                    'description' => 'Esta é uma tarefa criada para testar o envio de emails de delegação.',
                    'status' => 'pending',
                    'priority' => 'high',
                    'created_by' => $user->id,
                    'assigned_to' => $user->id,
                ]);

                $taskMail = new TaskAssignedMail($task, $user, $user);
                Mail::to($user->email)->send($taskMail);
                
                $this->info('✅ Email de delegação de tarefa enviado com sucesso!');
                $this->info("📋 Tarefa criada: {$task->title}");
            }

            $this->info("\n🎉 Teste de emails concluído!");
            $this->info("📬 Verifique a caixa de entrada de: {$user->email}");
            $this->info("📁 Se não receber, verifique a pasta de spam/lixo eletrônico");

        } catch (\Exception $e) {
            $this->error("❌ Erro ao enviar emails: " . $e->getMessage());
            $this->error("📋 Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}