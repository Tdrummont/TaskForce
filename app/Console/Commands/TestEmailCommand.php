<?php

namespace App\Console\Commands;

use App\Mail\UserRegisteredMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'tdrummontt@gmail.com';
        
        $this->info("🧪 Testando envio de email para: {$email}");
        
        // Teste 1: Email simples
        $this->info("📧 Teste 1: Email simples...");
        try {
            Mail::raw('Este é um teste de email do Task Force. Se você recebeu este email, o sistema está funcionando corretamente!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('🧪 Teste de Email - Task Force');
            });
            $this->info("✅ Email simples enviado com sucesso!");
        } catch (\Exception $e) {
            $this->error("❌ Erro no email simples: " . $e->getMessage());
            return 1;
        }
        
        // Teste 2: Email com template
        $this->info("📧 Teste 2: Email com template...");
        try {
            $user = User::first();
            if ($user) {
                Mail::to($email)->send(new UserRegisteredMail($user));
                $this->info("✅ Email com template enviado com sucesso!");
            } else {
                $this->warn("⚠️ Nenhum usuário encontrado para teste com template");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro no email com template: " . $e->getMessage());
        }
        
        // Teste 3: Verificar configuração
        $this->info("🔧 Teste 3: Verificando configuração...");
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_USERNAME', config('mail.mailers.smtp.username')],
                ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );
        
        $this->info("🎯 Verifique sua caixa de entrada e pasta de spam!");
        $this->info("📝 Se não receber os emails, verifique:");
        $this->line("   - Senha de aplicativo do Gmail está correta");
        $this->line("   - Verificação em duas etapas está ativada");
        $this->line("   - Emails não estão indo para spam");
        
        return 0;
    }
}