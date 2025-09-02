<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "🧪 Testando Rota de Notificações...\n\n";

// Verificar se as rotas estão registradas
echo "1. Verificando rotas registradas...\n";
$routes = shell_exec('php artisan route:list | grep notifications');
echo $routes;

echo "\n2. Verificando se há notificações no banco...\n";
$totalNotifications = DB::table('notifications')->count();
echo "   📊 Total de notificações: {$totalNotifications}\n";

if ($totalNotifications > 0) {
    echo "\n3. Verificando estrutura das notificações...\n";
    $sampleNotification = DB::table('notifications')->first();
    $data = json_decode($sampleNotification->data, true);
    
    echo "   📝 Exemplo de notificação:\n";
    echo "      ID: {$sampleNotification->id}\n";
    echo "      Tipo: {$sampleNotification->type}\n";
    echo "      Usuário: {$sampleNotification->notifiable_id}\n";
    echo "      Título: " . ($data['title'] ?? 'SEM TÍTULO') . "\n";
    echo "      Mensagem: " . ($data['message'] ?? 'SEM MENSAGEM') . "\n";
    
    echo "\n4. Verificando notificações para usuário ID 2...\n";
    $userNotifications = DB::table('notifications')
        ->where('notifiable_id', 2)
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    echo "   👤 Notificações para usuário ID 2: " . $userNotifications->count() . "\n";
    
    foreach ($userNotifications as $index => $notification) {
        $data = json_decode($notification->data, true);
        echo "   📝 Notificação " . ($index + 1) . ":\n";
        echo "      ID: {$notification->id}\n";
        echo "      Título: " . ($data['title'] ?? 'SEM TÍTULO') . "\n";
        echo "      Mensagem: " . ($data['message'] ?? 'SEM MENSAGEM') . "\n";
        echo "      Criada em: {$notification->created_at}\n";
        echo "      ---\n";
    }
}

echo "\n5. Testando rota web...\n";
$webRoute = shell_exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/notifications 2>/dev/null');
echo "   📡 GET /notifications: " . trim($webRoute) . "\n";

echo "\n6. Testando rota API...\n";
$apiRoute = shell_exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/notifications 2>/dev/null');
echo "   📡 GET /api/notifications: " . trim($apiRoute) . "\n";

echo "\n🎯 Diagnóstico:\n";
echo "========================\n";

if ($totalNotifications > 0) {
    echo "✅ Notificações existem no banco\n";
    
    $hasTitle = isset($data['title']) && !empty($data['title']);
    if ($hasTitle) {
        echo "✅ Notificações têm títulos\n";
    } else {
        echo "❌ Notificações não têm títulos\n";
    }
    
    if (trim($webRoute) === '302' || trim($webRoute) === '401') {
        echo "✅ Rota web funcionando (redireciona para login)\n";
    } else {
        echo "❌ Problema na rota web\n";
    }
    
    if (trim($apiRoute) === '302' || trim($apiRoute) === '401') {
        echo "✅ Rota API funcionando (redireciona para login)\n";
    } else {
        echo "❌ Problema na rota API\n";
    }
    
} else {
    echo "❌ Nenhuma notificação no banco\n";
}

echo "\n💡 Para testar no frontend:\n";
echo "1. Faça login no sistema\n";
echo "2. Abra o console do navegador (F12)\n";
echo "3. Clique no ícone de notificações\n";
echo "4. Verifique se há erros no console\n";
echo "5. Verifique se as requisições estão sendo feitas\n"; 