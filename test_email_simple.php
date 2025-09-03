<?php

/**
 * 🧪 Teste Simples de Email
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "🧪 Teste Simples de Email...\n\n";

try {
    // Verificar configuração
    echo "1️⃣ Configuração atual:\n";
    $config = config('mail');
    echo "   - Mailer: " . $config['default'] . "\n";
    echo "   - Host: " . $config['mailers']['smtp']['host'] . "\n";
    echo "   - Username: " . $config['mailers']['smtp']['username'] . "\n";
    echo "   - From Address: " . $config['from']['address'] . "\n";
    echo "   - From Name: " . $config['from']['name'] . "\n";
    echo "   - Encryption: " . ($config['mailers']['smtp']['encryption'] ?? 'não definido') . "\n\n";
    
    // Teste simples de envio de email
    echo "2️⃣ Testando envio de email simples...\n";
    
    try {
        // Enviar email de teste
        Mail::raw('Este é um email de teste para verificar se a configuração está funcionando.', function($message) {
            $message->to('tdrummontt@gmail.com')
                    ->subject('🧪 Teste de Email - Iron Force Tasks')
                    ->from('tdrummontt@gmail.com', 'Iron Force Tasks');
        });
        
        echo "   ✅ Email de teste enviado com sucesso!\n";
        echo "   📧 Para: tdrummontt@gmail.com\n";
        echo "   📧 Assunto: 🧪 Teste de Email - Iron Force Tasks\n";
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar email de teste: " . $e->getMessage() . "\n";
        echo "   📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    echo "\n🎉 Teste de email simples concluído!\n\n";
    
} catch (Exception $e) {
    echo "❌ Erro geral no teste: " . $e->getMessage() . "\n";
    echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
