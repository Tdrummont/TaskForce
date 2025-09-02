<?php

/**
 * 🧪 Teste de Email para Email Real do Usuário
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Log;

echo "🧪 Testando Envio de Email para Email Real...\n\n";

try {
    // Verificar configuração atual
    echo "1️⃣ Configuração atual:\n";
    $config = config('mail');
    echo "   - Mailer: " . $config['default'] . "\n";
    echo "   - Host: " . $config['mailers']['smtp']['host'] . "\n";
    echo "   - Username: " . $config['mailers']['smtp']['username'] . "\n";
    echo "   - From Address: " . $config['from']['address'] . "\n";
    echo "   - From Name: " . $config['from']['name'] . "\n\n";
    
    // Buscar usuário com email real
    echo "2️⃣ Buscando usuário com email real...\n";
    $realUser = User::where('email', 'tdrummontt@gmail.com')->first();
    
    if (!$realUser) {
        echo "   ❌ Usuário com email tdrummontt@gmail.com não encontrado!\n";
        echo "   📋 Usuários disponíveis:\n";
        $users = User::all(['id', 'name', 'email']);
        foreach ($users as $user) {
            echo "      - ID: {$user->id}, Nome: {$user->name}, Email: {$user->email}\n";
        }
        echo "\n   💡 Para testar, você precisa:\n";
        echo "      1. Fazer login no sistema web\n";
        echo "      2. Ou criar um usuário com seu email real\n";
        exit(1);
    }
    
    echo "   ✅ Usuário encontrado: {$realUser->name} ({$realUser->email})\n\n";
    
    // Testar notificação para email real
    echo "3️⃣ Testando notificação para email real...\n";
    
    try {
        $realUser->notify(new UserLoginNotification($realUser, '127.0.0.1', 'Teste Real'));
        echo "   ✅ Notificação enviada com sucesso!\n";
        
        // Verificar se foi salva no banco
        $recentNotification = $realUser->notifications()->latest()->first();
        if ($recentNotification) {
            echo "   ✅ Notificação salva no banco (ID: {$recentNotification->id})\n";
            echo "   📝 Tipo: {$recentNotification->type}\n";
            echo "   📅 Criada em: {$recentNotification->created_at}\n";
        }
        
        echo "\n🎉 Teste concluído com sucesso!\n";
        echo "📧 Email deve ter sido enviado para: {$realUser->email}\n";
        echo "💡 Verifique sua caixa de entrada e pasta de spam\n";
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar notificação: " . $e->getMessage() . "\n";
        
        if (strpos($e->getMessage(), 'Application-specific password required') !== false) {
            echo "\n🚨 PROBLEMA: Senha de aplicativo necessária!\n";
            echo "📋 SOLUÇÃO:\n";
            echo "1. Acesse: https://myaccount.google.com/security\n";
            echo "2. Ative verificação em duas etapas\n";
            echo "3. Gere senha para 'Email'\n";
            echo "4. Atualize MAIL_PASSWORD no .env\n";
        }
    }
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n🔍 Logs disponíveis em: storage/logs/laravel.log\n";
echo "🌐 Para testar no sistema web: php artisan serve\n"; 