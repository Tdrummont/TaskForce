<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestSmtpConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:smtp {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a conexão SMTP e envia um email de teste';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?? 'gabyribeiro001@gmail.com';
        
        $this->info("🧪 Testando conexão SMTP...");
        $this->info("📧 Email destinatário: {$email}");
        
        try {
            // Testar configuração SMTP
            $this->info("🔧 Configuração SMTP:");
            $this->info("   Host: " . config('mail.mailers.smtp.host'));
            $this->info("   Port: " . config('mail.mailers.smtp.port'));
            $this->info("   Username: " . config('mail.mailers.smtp.username'));
            $this->info("   Encryption: " . config('mail.mailers.smtp.encryption'));
            $this->info("   From: " . config('mail.from.address'));
            
            // Enviar email de teste simples
            $this->info("📤 Enviando email de teste...");
            
            Mail::raw('Este é um email de teste do sistema Iron Force Tasks. Se você recebeu este email, a configuração SMTP está funcionando corretamente.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('🧪 Teste de Conexão SMTP - Iron Force Tasks')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("✅ Email de teste enviado com sucesso!");
            $this->info("📧 Verifique a caixa de entrada (e spam) de: {$email}");
            
            Log::info('🧪 Teste SMTP executado com sucesso', [
                'email' => $email,
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port')
            ]);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao testar SMTP: " . $e->getMessage());
            
            Log::error('❌ Erro no teste SMTP', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
