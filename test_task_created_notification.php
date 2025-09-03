<?php

/**
 * 🧪 Teste de Notificação de Tarefa Criada
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use Illuminate\Support\Facades\Log;

echo "🧪 Testando Notificação de Tarefa Criada...\n\n";

try {
    // Verificar configuração
    echo "1️⃣ Configuração atual:\n";
    $config = config('mail');
    echo "   - Mailer: " . $config['default'] . "\n";
    echo "   - Host: " . $config['mailers']['smtp']['host'] . "\n";
    echo "   - Username: " . $config['mailers']['smtp']['username'] . "\n";
    echo "   - From Address: " . $config['from']['address'] . "\n";
    echo "   - From Name: " . $config['from']['name'] . "\n\n";
    
    // Buscar usuário real
    echo "2️⃣ Buscando usuário real...\n";
    $realUser = User::where('email', 'tdrummontt@gmail.com')->first();
    
    if (!$realUser) {
        echo "   ❌ Usuário com email tdrummontt@gmail.com não encontrado!\n";
        exit(1);
    }
    
    echo "   ✅ Usuário encontrado: {$realUser->name} ({$realUser->email})\n\n";
    
    // Teste: Notificação de Tarefa Criada
    echo "3️⃣ Testando Notificação de Tarefa Criada...\n";
    
    // Criar uma tarefa de teste
    $task = Task::create([
        'title' => 'Tarefa de Teste - Notificação de Criação',
        'description' => 'Esta é uma tarefa de teste para verificar se a notificação de tarefa criada está funcionando.',
        'status' => 'pending',
        'priority' => 'high',
        'category' => 'Teste',
        'estimated_hours' => 3,
        'due_date' => now()->addDays(5),
        'created_by' => $realUser->id,
        'assigned_to' => $realUser->id,
        'order' => 1
    ]);
    
    echo "   ✅ Tarefa de teste criada (ID: {$task->id})\n";
    
    try {
        $realUser->notify(new TaskCreatedNotification($task, $realUser));
        echo "   ✅ Notificação de tarefa criada enviada com sucesso!\n";
        echo "   📧 Assunto: 🎯 Nova Tarefa Criada: {$task->title}\n";
        echo "   📧 Para: {$realUser->email}\n";
        echo "   📧 Prioridade: 🔴 High\n";
        echo "   📧 Status: ⏳ Pending\n";
        echo "   📧 Data de Vencimento: " . $task->due_date->format('d/m/Y H:i') . "\n";
        
        // Verificar se foi salva no banco
        $recentNotification = $realUser->notifications()->latest()->first();
        if ($recentNotification && $recentNotification->type === 'App\Notifications\TaskCreatedNotification') {
            echo "   ✅ Notificação salva no banco (ID: {$recentNotification->id})\n";
        }
        
        // Verificar dados da notificação
        $notificationData = $recentNotification->data;
        echo "   📊 Dados da notificação:\n";
        echo "      - Task ID: " . $notificationData['task_id'] . "\n";
        echo "      - Task Title: " . $notificationData['task_title'] . "\n";
        echo "      - Creator ID: " . $notificationData['creator_id'] . "\n";
        echo "      - Creator Name: " . $notificationData['creator_name'] . "\n";
        echo "      - Priority: " . $notificationData['priority'] . "\n";
        echo "      - Status: " . $notificationData['status'] . "\n";
        echo "      - Type: " . $notificationData['type'] . "\n";
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar notificação de tarefa criada: " . $e->getMessage() . "\n";
        echo "   📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    echo "\n🎉 Teste de notificação de tarefa criada concluído!\n\n";
    
    // Limpar tarefa de teste
    $task->delete();
    echo "🧹 Tarefa de teste removida\n";
    
} catch (Exception $e) {
    echo "❌ Erro geral no teste: " . $e->getMessage() . "\n";
    echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
