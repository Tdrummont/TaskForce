<?php

/**
 * 🔧 Script para Atualizar Configurações de Email no .env
 * 
 * Este script atualiza automaticamente as configurações de email
 * Execute: php atualizar_env.php
 */

echo "🔧 Atualizando Configurações de Email no .env...\n\n";

// Ler o arquivo .env atual
$envFile = '.env';
$envContent = file_get_contents($envFile);

if (!$envContent) {
    echo "❌ Erro: Não foi possível ler o arquivo .env\n";
    exit(1);
}

echo "📋 Configurações atuais encontradas:\n";
echo "   - MAIL_PASSWORD: " . (preg_match('/MAIL_PASSWORD=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n";
echo "   - MAIL_FROM_ADDRESS: " . (preg_match('/MAIL_FROM_ADDRESS=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n";
echo "   - MAIL_FROM_NAME: " . (preg_match('/MAIL_FROM_NAME=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n\n";

echo "🚨 ATENÇÃO: Você precisa fornecer a senha de aplicativo do Google!\n\n";

echo "📱 Para obter a senha de aplicativo:\n";
echo "1. Acesse: https://myaccount.google.com/security\n";
echo "2. Ative 'Verificação em duas etapas' (se não estiver ativa)\n";
echo "3. Vá para 'Senhas de app'\n";
echo "4. Gere senha para 'Email'\n";
echo "5. Copie a senha de 16 caracteres\n\n";

echo "🔑 Digite a senha de aplicativo (16 caracteres): ";
$handle = fopen("php://stdin", "r");
$appPassword = trim(fgets($handle));
fclose($handle);

if (strlen($appPassword) < 16) {
    echo "❌ Erro: A senha de aplicativo deve ter pelo menos 16 caracteres\n";
    exit(1);
}

echo "\n✅ Senha de aplicativo recebida!\n\n";

// Fazer backup do arquivo atual
$backupFile = '.env.backup.' . date('Y-m-d_H-i-s');
if (copy($envFile, $backupFile)) {
    echo "💾 Backup criado: $backupFile\n";
}

// Atualizar as configurações
$updates = [
    'MAIL_PASSWORD=' . $appPassword,
    'MAIL_FROM_ADDRESS=tdrummontt@gmail.com',
    'MAIL_FROM_NAME="Iron Force Tasks"'
];

foreach ($updates as $update) {
    $key = explode('=', $update)[0];
    
    if (preg_match("/^{$key}=/m", $envContent)) {
        // Substituir linha existente
        $envContent = preg_replace("/^{$key}=.*/m", $update, $envContent);
        echo "✅ Atualizado: $key\n";
    } else {
        // Adicionar nova linha
        $envContent .= "\n" . $update;
        echo "➕ Adicionado: $key\n";
    }
}

// Salvar arquivo atualizado
if (file_put_contents($envFile, $envContent)) {
    echo "\n🎉 Arquivo .env atualizado com sucesso!\n\n";
    
    echo "📋 Configurações atualizadas:\n";
    echo "   - MAIL_PASSWORD: " . $appPassword . "\n";
    echo "   - MAIL_FROM_ADDRESS: tdrummontt@gmail.com\n";
    echo "   - MAIL_FROM_NAME: Iron Force Tasks\n\n";
    
    echo "🧪 Próximos passos:\n";
    echo "1. Limpar cache: php artisan config:clear\n";
    echo "2. Testar email: php test_email_fix.php\n";
    echo "3. Testar sistema web\n\n";
    
    echo "💡 Dica: Se der erro, verifique se:\n";
    echo "   - A verificação em duas etapas está ativa\n";
    echo "   - A senha de aplicativo foi gerada para 'Email'\n";
    echo "   - Não há espaços extras na senha\n";
    
} else {
    echo "❌ Erro: Não foi possível salvar o arquivo .env\n";
    exit(1);
}

echo "\n🚀 Pronto! Agora teste o sistema de email.\n"; 