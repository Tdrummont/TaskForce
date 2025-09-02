<?php

/**
 * Script de Configuração do Sistema de Notificações
 * 
 * Este script ajuda a configurar as variáveis de ambiente necessárias.
 * Execute: php setup_notifications.php
 */

echo "🚀 Configuração do Sistema de Notificações\n";
echo "==========================================\n\n";

// Verificar se o arquivo .env existe
$envFile = '.env';
if (!file_exists($envFile)) {
    echo "❌ Arquivo .env não encontrado!\n";
    echo "📝 Crie um arquivo .env na raiz do projeto.\n\n";
    exit(1);
}

echo "✅ Arquivo .env encontrado\n\n";

// Verificar variáveis do Pusher
$requiredVars = [
    'PUSHER_APP_ID' => 'ID do app Pusher',
    'PUSHER_APP_KEY' => 'Chave do app Pusher', 
    'PUSHER_APP_SECRET' => 'Segredo do app Pusher',
    'PUSHER_APP_CLUSTER' => 'Cluster do Pusher (ex: mt1)',
    'BROADCAST_DRIVER' => 'Driver de broadcast (deve ser: pusher)',
    'VITE_PUSHER_APP_KEY' => 'Chave do Pusher para Vite',
    'VITE_PUSHER_APP_CLUSTER' => 'Cluster do Pusher para Vite'
];

echo "🔍 Verificando variáveis de ambiente...\n";
echo "----------------------------------------\n";

$missingVars = [];
$envContent = file_get_contents($envFile);

foreach ($requiredVars as $var => $description) {
    if (strpos($envContent, $var . '=') !== false) {
        // Verificar se tem valor
        if (preg_match("/^{$var}=(.*)$/m", $envContent, $matches)) {
            $value = trim($matches[1]);
            if (!empty($value) && $value !== 'your_' . strtolower($var)) {
                echo "✅ {$var}: {$value}\n";
            } else {
                echo "⚠️  {$var}: Não configurado (valor padrão)\n";
                $missingVars[] = $var;
            }
        } else {
            echo "❌ {$var}: Definido mas sem valor\n";
            $missingVars[] = $var;
        }
    } else {
        echo "❌ {$var}: Não encontrado\n";
        $missingVars[] = $var;
    }
}

echo "\n";

if (empty($missingVars)) {
    echo "🎉 Todas as variáveis estão configuradas!\n";
    echo "🚀 O sistema de notificações deve funcionar.\n";
} else {
    echo "⚠️  Variáveis que precisam ser configuradas:\n";
    echo "--------------------------------------------\n";
    
    foreach ($missingVars as $var) {
        echo "• {$var}\n";
    }
    
    echo "\n📝 Exemplo de configuração para o .env:\n";
    echo "----------------------------------------\n";
    echo "# Broadcasting\n";
    echo "BROADCAST_DRIVER=pusher\n\n";
    echo "# Pusher Configuration\n";
    echo "PUSHER_APP_ID=1234567\n";
    echo "PUSHER_APP_KEY=abcdef123456\n";
    echo "PUSHER_APP_SECRET=ghijkl789012\n";
    echo "PUSHER_APP_CLUSTER=mt1\n\n";
    echo "# Vite Environment Variables\n";
    echo "VITE_PUSHER_APP_KEY=\${PUSHER_APP_KEY}\n";
    echo "VITE_PUSHER_APP_CLUSTER=\${PUSHER_APP_CLUSTER}\n\n";
    
    echo "🔗 Obtenha as credenciais em: https://pusher.com\n";
    echo "📚 Documentação: https://pusher.com/docs\n";
}

echo "\n🔧 Próximos passos:\n";
echo "1. Configure as variáveis no arquivo .env\n";
echo "2. Execute: php artisan config:clear\n";
echo "3. Execute: npm run build\n";
echo "4. Execute: php artisan serve\n";
echo "5. Teste o sistema de notificações\n";

echo "\n🧪 Para testar: php test_notifications.php\n"; 