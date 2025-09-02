<?php

/**
 * 🔧 Script Simples para Atualizar .env
 */

echo "🔧 Atualizando configurações de email no .env...\n\n";

// Ler o arquivo .env
$envFile = '.env';
$envContent = file_get_contents($envFile);

if (!$envContent) {
    echo "❌ Erro: Não foi possível ler o arquivo .env\n";
    exit(1);
}

echo "📋 Configurações atuais:\n";
echo "   - MAIL_PASSWORD: " . (preg_match('/MAIL_PASSWORD=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n";
echo "   - MAIL_FROM_ADDRESS: " . (preg_match('/MAIL_FROM_ADDRESS=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n";
echo "   - MAIL_FROM_NAME: " . (preg_match('/MAIL_FROM_NAME=(.+)/', $envContent, $matches) ? $matches[1] : 'NÃO ENCONTRADO') . "\n\n";

// Configurações que você forneceu
$email = 'tdrummontt@gmail.com';
$password = 'yggdracode2505';

echo "🔄 Atualizando para:\n";
echo "   - MAIL_PASSWORD: {$password}\n";
echo "   - MAIL_FROM_ADDRESS: {$email}\n";
echo "   - MAIL_FROM_NAME: Iron Force Tasks\n\n";

// Atualizar as configurações
$updates = [
    'MAIL_PASSWORD=' . $password,
    'MAIL_FROM_ADDRESS=' . $email,
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
    echo "   - MAIL_PASSWORD: {$password}\n";
    echo "   - MAIL_FROM_ADDRESS: {$email}\n";
    echo "   - MAIL_FROM_NAME: Iron Force Tasks\n\n";
    
    echo "🧪 Próximos passos:\n";
    echo "1. Limpar cache: php artisan config:clear\n";
    echo "2. Testar email: php test_email_fix.php\n";
    echo "3. Testar sistema web\n\n";
    
    echo "⚠️  ATENÇÃO: A senha '{$password}' pode não funcionar mais!\n";
    echo "📱 Para funcionar, você precisa:\n";
    echo "   1. Ativar verificação em duas etapas no Google\n";
    echo "   2. Gerar uma senha de aplicativo\n";
    echo "   3. Usar a senha de aplicativo no lugar de '{$password}'\n\n";
    
    echo "🚀 Pronto! Agora teste o sistema.\n";
    
} else {
    echo "❌ Erro: Não foi possível salvar o arquivo .env\n";
    exit(1);
} 