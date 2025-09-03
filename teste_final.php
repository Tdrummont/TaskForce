<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Teste Final - Configurações Corrigidas\n";
echo "==========================================\n\n";

echo "🔍 Verificando configurações:\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_SCHEME: " . config('mail.mailers.smtp.scheme') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "   MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "   MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "   MAIL_FROM_NAME: " . config('mail.from.name') . "\n";
echo "   BROADCAST_DRIVER: " . config('broadcasting.default') . "\n\n";

echo "📧 Enviando email de teste...\n";

try {
    Mail::raw('Teste final - Configurações corrigidas!', function($message) {
        $message->to('tdrummontt@gmail.com')
                ->subject('✅ Configurações Corrigidas - ' . date('H:i:s'));
    });
    
    echo "✅ Email enviado com sucesso!\n";
    echo "📧 Verifique: tdrummontt@gmail.com\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🚀 Teste final concluído!\n";
