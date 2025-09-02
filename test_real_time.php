<?php

/**
 * Teste em Tempo Real do Sistema de Notificações
 * 
 * Este script testa se as notificações estão funcionando em tempo real.
 * Execute: php test_real_time.php
 */

require_once 'vendor/autoload.php';

use App\Events\TaskAssigned;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Teste em Tempo Real do Sistema de Notificações\n";
echo "================================================\n\n";

try {
    // 1. Verificar se há usuários no sistema
    echo "1. Verificando usuários no sistema...\n";
    
    $users = User::all();
    if ($users->count() < 2) {
        echo "   ❌ Preciso de pelo menos 2 usuários para testar.\n";
        echo "   💡 Crie alguns usuários primeiro ou use o seeder.\n\n";
        exit(1);
    }
    
    $user1 = $users->first();
    $user2 = $users->where('id', '!=', $user1->id)->first();
    
    echo "   ✅ Usuário 1: {$user1->name} (ID: {$user1->id})\n";
    echo "   ✅ Usuário 2: {$user2->name} (ID: {$user2->id})\n\n";
    
    // 2. Verificar se há tarefas
    echo "2. Verificando tarefas no sistema...\n";
    
    $task = Task::where('assigned_to', $user2->id)->first();
    if (!$task) {
        echo "   📝 Criando tarefa de teste...\n";
        
        $task = Task::create([
            'title' => 'Tarefa de Teste - ' . now()->format('H:i:s'),
            'description' => 'Esta é uma tarefa de teste para verificar notificações',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => $user2->id,
            'created_by' => $user1->id
        ]);
        
        echo "   ✅ Tarefa criada: {$task->title} (ID: {$task->id})\n";
    } else {
        echo "   ✅ Tarefa encontrada: {$task->title} (ID: {$task->id})\n";
    }
    
    echo "\n";
    
    // 3. Testar disparo do evento
    echo "3. Testando disparo do evento em tempo real...\n";
    
    echo "   📡 Disparando evento TaskAssigned...\n";
    echo "   📡 Canal: user.{$user2->id}\n";
    echo "   📡 Evento: task.assigned\n";
    
    // Disparar o evento
    event(new TaskAssigned($task, $user1, $user2));
    
    echo "   ✅ Evento disparado!\n\n";
    
    // 4. Verificar logs
    echo "4. Verificando logs...\n";
    
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $recentLogs = array_slice(explode("\n", $logs), -20);
        
        $relevantLogs = array_filter($recentLogs, function($log) {
            return strpos($log, 'TaskAssigned') !== false || 
                   strpos($log, 'tarefa atribuída') !== false ||
                   strpos($log, 'broadcast') !== false;
        });
        
        if (!empty($relevantLogs)) {
            echo "   📋 Logs relevantes encontrados:\n";
            foreach ($relevantLogs as $log) {
                if (!empty(trim($log))) {
                    echo "      " . trim($log) . "\n";
                }
            }
        } else {
            echo "   ℹ️  Nenhum log relevante encontrado recentemente.\n";
        }
    } else {
        echo "   ⚠️  Arquivo de log não encontrado.\n";
    }
    
    echo "\n";
    
    // 5. Instruções para teste manual
    echo "5. Instruções para teste manual:\n";
    echo "   📱 Abra o navegador e acesse: http://localhost:8000\n";
    echo "   🔐 Faça login com o usuário: {$user2->email}\n";
    echo "   🔍 Abra o console do navegador (F12)\n";
    echo "   📡 Procure por mensagens de log do componente de notificações\n";
    echo "   🎯 Execute este script novamente para disparar uma nova notificação\n";
    
    echo "\n";
    
    // 6. Status do sistema
    echo "6. Status do sistema:\n";
    echo "   🔧 Broadcasting: " . (config('broadcasting.default') === 'pusher' ? '✅ Pusher' : '❌ ' . config('broadcasting.default')) . "\n";
    echo "   📡 Rotas de broadcast: " . (file_exists(public_path('broadcasting/auth')) ? '✅' : '❌') . "\n";
    echo "   📧 Mail: " . (config('mail.default') ? '✅ ' . config('mail.default') : '❌') . "\n";
    echo "   🎯 Evento TaskAssigned: ✅ Carregado\n";
    
    echo "\n🎉 Teste concluído!\n";
    echo "💡 Se as notificações não aparecerem, verifique:\n";
    echo "   1. Console do navegador para erros\n";
    echo "   2. Logs do Laravel em storage/logs/laravel.log\n";
    echo "   3. Se o Pusher está configurado corretamente\n";
    echo "   4. Se o usuário está autenticado\n";
    
} catch (Exception $e) {
    echo "\n❌ Erro durante o teste: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 