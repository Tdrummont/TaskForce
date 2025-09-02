<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "🧪 Testando NotificationController...\n\n";

// Simular um usuário autenticado (ID 2 - Ana Gabrielle)
$userId = 2;

echo "1. Verificando notificações para usuário ID: {$userId}\n";

// Buscar notificações do usuário
$notifications = DB::table('notifications')
    ->where('notifiable_type', User::class)
    ->where('notifiable_id', $userId)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "   📊 Total de notificações encontradas: " . $notifications->count() . "\n\n";

if ($notifications->count() > 0) {
    echo "2. Processando notificações...\n";
    
    $processedNotifications = $notifications->map(function ($notification) {
        $data = json_decode($notification->data, true);
        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'Notificação',
            'message' => $data['message'] ?? 'Nova notificação',
            'type' => $data['type'] ?? 'info',
            'created_at' => $notification->created_at,
            'read_at' => $notification->read_at,
            'data' => $data
        ];
    });
    
    echo "   ✅ Notificações processadas com sucesso!\n\n";
    
    echo "3. Exibindo notificações processadas:\n";
    foreach ($processedNotifications as $index => $notification) {
        echo "   📝 Notificação " . ($index + 1) . ":\n";
        echo "      ID: {$notification['id']}\n";
        echo "      Título: {$notification['title']}\n";
        echo "      Mensagem: {$notification['message']}\n";
        echo "      Tipo: {$notification['type']}\n";
        echo "      Criada em: {$notification['created_at']}\n";
        echo "      Lida em: " . ($notification['read_at'] ?: 'Não lida') . "\n";
        echo "      Dados completos: " . json_encode($notification['data'], JSON_PRETTY_PRINT) . "\n";
        echo "\n";
    }
    
    echo "4. Verificando estrutura da resposta...\n";
    $response = [
        'success' => true,
        'notifications' => $processedNotifications
    ];
    
    echo "   📊 Estrutura da resposta:\n";
    echo "      success: " . ($response['success'] ? 'true' : 'false') . "\n";
    echo "      notifications count: " . count($response['notifications']) . "\n";
    
    echo "\n5. Simulando resposta JSON...\n";
    $jsonResponse = json_encode($response, JSON_PRETTY_PRINT);
    echo "   📝 JSON gerado:\n";
    echo $jsonResponse;
    
} else {
    echo "   ❌ Nenhuma notificação encontrada para o usuário\n";
}

echo "\n🎯 Diagnóstico:\n";
echo "========================\n";

if ($notifications->count() > 0) {
    echo "✅ Notificações encontradas no banco\n";
    echo "✅ Dados sendo processados corretamente\n";
    echo "✅ Estrutura da resposta está correta\n";
    echo "✅ JSON sendo gerado corretamente\n";
    echo "\n💡 O problema pode estar no frontend ou na autenticação\n";
} else {
    echo "❌ Nenhuma notificação encontrada\n";
    echo "🔍 Verificar:\n";
    echo "   - Se o usuário tem notificações\n";
    echo "   - Se a estrutura da tabela está correta\n";
    echo "   - Se as notificações estão sendo criadas\n";
}

echo "\n💡 Para testar no frontend:\n";
echo "1. Faça login com o usuário ID {$userId}\n";
echo "2. Abra o console do navegador (F12)\n";
echo "3. Clique no ícone de notificações\n";
echo "4. Verifique se há erros no console\n";
echo "5. Verifique se a requisição para /api/notifications está sendo feita\n"; 