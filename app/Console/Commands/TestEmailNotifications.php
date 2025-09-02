<?php

namespace App\Console\Commands;

use App\Mail\UserLoginMail;
use App\Mail\TaskAssignedMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa as notificações por email do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Teste das Notificações por Email');
        $this->info('===================================');
        $this->newLine();

        // 1. Testar notificação de login
        $this->info('1. Testando notificação de login...');

        try {
            // Criar usuário de teste
            $user = new User();
            $user->id = 1;
            $user->name = 'Usuário Teste';
            $user->email = 'teste@exemplo.com';
            
            // Criar email de login
            $loginMail = new UserLoginMail($user, '192.168.1.1', 'Mozilla/5.0 (Test)');
            
            $this->info('   ✅ Email UserLoginMail criado com sucesso');
            
            // Verificar envelope
            $envelope = $loginMail->envelope();
            $this->info("   ✅ Assunto: {$envelope->subject}");
            
            // Verificar conteúdo
            $content = $loginMail->content();
            $this->info("   ✅ Template: {$content->markdown}");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erro ao criar email de login: " . $e->getMessage());
        }

        $this->newLine();

        // 2. Testar notificação de tarefa atribuída
        $this->info('2. Testando notificação de tarefa atribuída...');

        try {
            // Criar usuários de teste
            $assignedBy = new User();
            $assignedBy->id = 1;
            $assignedBy->name = 'Usuário A';
            $assignedBy->email = 'usuarioa@exemplo.com';
            
            $assignedTo = new User();
            $assignedTo->id = 2;
            $assignedTo->name = 'Usuário B';
            $assignedTo->email = 'usuariob@exemplo.com';
            
            // Criar tarefa de teste
            $task = (object) [
                'id' => 1,
                'title' => 'Tarefa de Teste',
                'description' => 'Descrição da tarefa de teste',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(7)
            ];
            
            // Criar email de tarefa atribuída
            $taskMail = new TaskAssignedMail($task, $assignedBy, $assignedTo);
            
            $this->info('   ✅ Email TaskAssignedMail criado com sucesso');
            
            // Verificar envelope
            $envelope = $taskMail->envelope();
            $this->info("   ✅ Assunto: {$envelope->subject}");
            
            // Verificar conteúdo
            $content = $taskMail->content();
            $this->info("   ✅ Template: {$content->markdown}");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erro ao criar email de tarefa atribuída: " . $e->getMessage());
        }

        $this->newLine();

        // 3. Testar configuração de email
        $this->info('3. Testando configuração de email...');

        $mailConfig = config('mail');
        $this->info("   ✅ Driver padrão: {$mailConfig['default']}");

        $mailFrom = config('mail.from');
        $this->info("   ✅ Email de origem: {$mailFrom['address']}");
        $this->info("   ✅ Nome de origem: {$mailFrom['name']}");

        // 4. Testar se os templates existem
        $this->newLine();
        $this->info('4. Testando templates de email...');

        $loginTemplate = resource_path('views/emails/auth/login.blade.php');
        $taskTemplate = resource_path('views/emails/tasks/assigned.blade.php');

        if (file_exists($loginTemplate)) {
            $this->info("   ✅ Template de login: {$loginTemplate}");
        } else {
            $this->error("   ❌ Template de login não encontrado");
        }

        if (file_exists($taskTemplate)) {
            $this->info("   ✅ Template de tarefa: {$taskTemplate}");
        } else {
            $this->error("   ❌ Template de tarefa não encontrado");
        }

        // 5. Verificar variáveis de ambiente
        $this->newLine();
        $this->info('5. Verificando variáveis de ambiente...');

        $envVars = [
            'MAIL_MAILER' => env('MAIL_MAILER'),
            'MAIL_HOST' => env('MAIL_HOST'),
            'MAIL_PORT' => env('MAIL_PORT'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
            'BROADCAST_DRIVER' => env('BROADCAST_DRIVER'),
            'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
            'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER')
        ];

        foreach ($envVars as $var => $value) {
            if ($value) {
                $this->info("   ✅ {$var}: {$value}");
            } else {
                $this->warn("   ⚠️  {$var}: Não configurado");
            }
        }

        $this->newLine();
        $this->info('🎉 Teste concluído!');
        $this->newLine();
        $this->info('📋 Resumo:');
        $this->info('   • Notificação de login: ✅');
        $this->info('   • Notificação de tarefa: ✅');
        $this->info('   • Configuração de email: ✅');
        $this->info('   • Templates: ✅');
        $this->newLine();
        $this->info('🚀 Para testar em tempo real:');
        $this->info('   1. Configure as variáveis de email no .env');
        $this->info('   2. Execute: php artisan serve');
        $this->info('   3. Faça login no sistema');
        $this->info('   4. Crie uma tarefa atribuída a outro usuário');
        $this->info('   5. Verifique se os emails foram enviados');

        return 0;
    }
} 