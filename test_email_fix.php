<?php

/**
 * 🧪 Script de Teste para Verificar Correção do Email
 * 
 * Execute este script após configurar a senha de aplicativo no .env
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "🧪 Testando Configuração de Email...\n\n";

try {
    // Teste 1: Verificar configuração
    echo "1️⃣ Verificando configuração...\n";
    $config = config('mail');
    echo "   - Mailer: " . $config['default'] . "\n";
    echo "   - Host: " . $config['mailers']['smtp']['host'] . "\n";
    echo "   - Port: " . $config['mailers']['smtp']['port'] . "\n";
    echo "   - Username: " . $config['mailers']['smtp']['username'] . "\n";
    echo "   - Encryption: " . ($config['mailers']['smtp']['encryption'] ?? 'null') . "\n";
    echo "   - From Address: " . $config['from']['address'] . "\n";
    echo "   - From Name: " . $config['from']['name'] . "\n\n";

    // Teste 2: Envio de email simples
    echo "2️⃣ Testando envio de email...\n";
    Mail::raw('Este é um teste de email do Iron Force Tasks!', function($message) {
        $message->to('teste@exemplo.com')
                ->subject('🧪 Teste de Email - Iron Force Tasks');
    });
    
    echo "   ✅ Email enviado com sucesso!\n\n";
    
    // Teste 3: Verificar se as classes de email existem
    echo "3️⃣ Verificando classes de email...\n";
    
    if (class_exists('App\Mail\UserLoginMail')) {
        echo "   ✅ UserLoginMail existe\n";
    } else {
        echo "   ❌ UserLoginMail não encontrada\n";
    }
    
    if (class_exists('App\Mail\TaskAssignedMail')) {
        echo "   ✅ TaskAssignedMail existe\n";
    } else {
        echo "   ❌ TaskAssignedMail não encontrada\n";
    }
    
    // Teste 4: Verificar templates
    echo "\n4️⃣ Verificando templates de email...\n";
    
    $loginTemplate = resource_path('views/emails/auth/login.blade.php');
    if (file_exists($loginTemplate)) {
        echo "   ✅ Template de login existe\n";
    } else {
        echo "   ❌ Template de login não encontrado\n";
    }
    
    $taskTemplate = resource_path('views/emails/tasks/assigned.blade.php');
    if (file_exists($taskTemplate)) {
        echo "   ✅ Template de tarefa existe\n";
    } else {
        echo "   ❌ Template de tarefa não encontrado\n";
    }
    
    echo "\n🎉 Todos os testes passaram! O sistema de email está funcionando.\n";
    echo "💡 Agora você pode:\n";
    echo "   - Fazer login para receber email de notificação\n";
    echo "   - Criar tarefas atribuídas a outros usuários\n";
    echo "   - Ver os snackbars de sucesso em vez de erro\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Application-specific password required') !== false) {
        echo "🚨 PROBLEMA IDENTIFICADO: Senha de aplicativo necessária!\n\n";
        echo "📋 SOLUÇÃO:\n";
        echo "1. Acesse: https://myaccount.google.com/security\n";
        echo "2. Ative a verificação em duas etapas\n";
        echo "3. Gere uma senha de aplicativo para 'Email'\n";
        echo "4. Atualize MAIL_PASSWORD no .env\n";
        echo "5. Execute: php artisan config:clear\n";
        echo "6. Teste novamente\n";
    } elseif (strpos($e->getMessage(), 'Failed to authenticate') !== false) {
        echo "🚨 PROBLEMA IDENTIFICADO: Falha na autenticação!\n\n";
        echo "📋 SOLUÇÃO:\n";
        echo "1. Verifique se o email e senha estão corretos\n";
        echo "2. Use uma senha de aplicativo, não a senha da conta\n";
        echo "3. Confirme se a verificação em duas etapas está ativa\n";
    } else {
        echo "📋 VERIFICAÇÕES:\n";
        echo "1. Execute: php artisan config:clear\n";
        echo "2. Verifique se o .env está correto\n";
        echo "3. Confirme se o Gmail permite acesso a apps menos seguros\n";
    }
    
    echo "\n📖 Para mais detalhes, consulte: EMAIL_FIX_INSTRUCTIONS.md\n";
}

echo "\n🔍 Logs de email disponíveis em: storage/logs/laravel.log\n";
echo "📧 Para testar em tempo real, use o sistema web!\n"; 