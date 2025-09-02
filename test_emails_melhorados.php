<?php

/**
 * 🧪 Teste dos Emails Melhorados
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Task;
use App\Notifications\UserLoginNotification;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Log;

echo "🧪 Testando Emails Melhorados...\n\n";

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
    
    // Teste 1: Email de Login Melhorado
    echo "3️⃣ Testando Email de Login Melhorado...\n";
    
    try {
        $realUser->notify(new UserLoginNotification($realUser, '192.168.1.100', 'Chrome 120.0.0.0'));
        echo "   ✅ Email de login enviado com sucesso!\n";
        echo "   📧 Assunto: 🔔 Nova Atividade Detectada - Iron Force Tasks\n";
        echo "   📧 Para: {$realUser->email}\n";
        
        // Verificar se foi salva no banco
        $recentNotification = $realUser->notifications()->latest()->first();
        if ($recentNotification) {
            echo "   ✅ Notificação salva no banco (ID: {$recentNotification->id})\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar email de login: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Teste 2: Email de Tarefa Melhorado
    echo "4️⃣ Testando Email de Tarefa Melhorado...\n";
    
    // Criar uma tarefa de teste
    $task = Task::first();
    if (!$task) {
        echo "   ⚠️ Nenhuma tarefa encontrada, criando uma de teste...\n";
        
        $task = Task::create([
            'title' => 'Tarefa de Teste - Email Melhorado',
            'description' => 'Esta é uma tarefa de teste para verificar o email melhorado com emojis e formatação.',
            'status' => 'pending',
            'priority' => 'high',
            'category' => 'Teste',
            'estimated_hours' => 2,
            'due_date' => now()->addDays(3),
            'created_by' => $realUser->id,
            'assigned_to' => $realUser->id,
            'order' => 1
        ]);
        
        echo "   ✅ Tarefa de teste criada (ID: {$task->id})\n";
    }
    
    try {
        $realUser->notify(new TaskAssignedNotification($task, $realUser, $realUser));
        echo "   ✅ Email de tarefa enviado com sucesso!\n";
        echo "   📧 Assunto: 🎯 Nova Tarefa Atribuída: {$task->title}\n";
        echo "   📧 Para: {$realUser->email}\n";
        echo "   📧 Prioridade: 🔴 High\n";
        echo "   📧 Status: ⏳ Pending\n";
        
        // Verificar se foi salva no banco
        $recentTaskNotification = $realUser->notifications()->latest()->first();
        if ($recentTaskNotification && $recentTaskNotification->type === 'App\Notifications\TaskAssignedNotification') {
            echo "   ✅ Notificação de tarefa salva no banco (ID: {$recentTaskNotification->id})\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar email de tarefa: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Teste dos emails melhorados concluído!\n\n";
    
    echo "📧 Emails enviados:\n";
    echo "   1. 🔔 Nova Atividade Detectada (Login)\n";
    echo "   2. 🎯 Nova Tarefa Atribuída\n\n";
    
    echo "💡 Verifique sua caixa de entrada em: {$realUser->email}\n";
    echo "🔍 Verifique também a pasta de spam\n";
    
    echo "\n🚀 Próximos passos:\n";
    echo "   1. Verifique se os emails chegaram\n";
    echo "   2. Teste o sistema web: php artisan serve\n";
    echo "   3. Faça login e crie tarefas para ver os emails em ação\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n🔍 Logs disponíveis em: storage/logs/laravel.log\n";
echo "🌐 Para testar no sistema web: php artisan serve\n"; 