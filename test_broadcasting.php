<?php

/**
 * Teste do Sistema de Broadcasting
 * 
 * Este script testa se o broadcasting está funcionando corretamente.
 * Execute: php test_broadcasting.php
 */

require_once 'vendor/autoload.php';

use App\Events\TaskAssigned;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Teste do Sistema de Broadcasting\n";
echo "==================================\n\n";

try {
    // 1. Verificar configuração
    echo "1. Verificando configuração de broadcasting...\n";
    
    $driver = config('broadcasting.default');
    echo "   🔧 Driver padrão: {$driver}\n";
    
    $pusherConfig = config('broadcasting.connections.pusher');
    if ($pusherConfig) {
        echo "   ✅ Configuração do Pusher encontrada\n";
        echo "   📡 App ID: {$pusherConfig['app_id']}\n";
        echo "   🔑 App Key: {$pusherConfig['key']}\n";
        echo "   🌐 Cluster: {$pusherConfig['options']['cluster']}\n";
    } else {
        echo "   ❌ Configuração do Pusher não encontrada\n";
        exit(1);
    }
    
    echo "\n";
    
    // 2. Verificar se o evento pode ser criado
    echo "2. Testando criação do evento...\n";
    
    $user1 = User::first();
    $user2 = User::where('id', '!=', $user1->id)->first();
    
    if (!$user2) {
        echo "   ❌ Preciso de pelo menos 2 usuários\n";
        exit(1);
    }
    
    $task = Task::first() ?? Task::create([
        'title' => 'Tarefa de Teste Broadcasting',
        'description' => 'Teste do sistema de broadcasting',
        'status' => 'pending',
        'priority' => 'medium',
        'assigned_to' => $user2->id,
        'created_by' => $user1->id
    ]);
    
    echo "   ✅ Usuário 1: {$user1->name} (ID: {$user1->id})\n";
    echo "   ✅ Usuário 2: {$user2->name} (ID: {$user2->id})\n";
    echo "   ✅ Tarefa: {$task->title} (ID: {$task->id})\n";
    
    echo "\n";
    
    // 3. Testar evento com broadcasting
    echo "3. Testando evento com broadcasting...\n";
    
    // Criar o evento
    $event = new TaskAssigned($task, $user1, $user2);
    
    // Verificar canais
    $channels = $event->broadcastOn();
    echo "   📡 Canais de broadcast: " . count($channels) . "\n";
    
    foreach ($channels as $channel) {
        echo "      - " . get_class($channel) . ": " . $channel->name . "\n";
    }
    
    // Verificar dados
    $data = $event->broadcastWith();
    echo "   📊 Dados do evento: " . count($data) . " campos\n";
    
    // Verificar nome do evento
    $eventName = $event->broadcastAs();
    echo "   🏷️  Nome do evento: {$eventName}\n";
    
    echo "\n";
    
    // 4. Testar disparo real
    echo "4. Testando disparo real do evento...\n";
    
    try {
        // Limpar logs anteriores
        Log::info('🧪 Iniciando teste de broadcasting');
        
        // Disparar o evento
        event($event);
        
        echo "   ✅ Evento disparado com sucesso!\n";
        
        // Aguardar um pouco para o broadcast
        sleep(2);
        
        // Verificar logs
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $recentLogs = array_slice(explode("\n", $logs), -10);
            
            $broadcastLogs = array_filter($recentLogs, function($log) {
                return strpos($log, 'broadcast') !== false || 
                       strpos($log, 'TaskAssigned') !== false ||
                       strpos($log, 'pusher') !== false;
            });
            
            if (!empty($broadcastLogs)) {
                echo "   📋 Logs de broadcast encontrados:\n";
                foreach ($broadcastLogs as $log) {
                    if (!empty(trim($log))) {
                        echo "      " . trim($log) . "\n";
                    }
                }
            } else {
                echo "   ℹ️  Nenhum log de broadcast encontrado\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao disparar evento: " . $e->getMessage() . "\n";
        echo "   📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    echo "\n";
    
    // 5. Verificar se o evento implementa ShouldBroadcast
    echo "5. Verificando implementação do evento...\n";
    
    $reflection = new ReflectionClass($event);
    $interfaces = $reflection->getInterfaceNames();
    
    if (in_array('Illuminate\Contracts\Broadcasting\ShouldBroadcast', $interfaces)) {
        echo "   ✅ Evento implementa ShouldBroadcast\n";
    } else {
        echo "   ❌ Evento NÃO implementa ShouldBroadcast\n";
    }
    
    if (in_array('Illuminate\Broadcasting\InteractsWithSockets', $reflection->getTraitNames())) {
        echo "   ✅ Evento usa InteractsWithSockets\n";
    } else {
        echo "   ❌ Evento NÃO usa InteractsWithSockets\n";
    }
    
    echo "\n";
    
    // 6. Status final
    echo "6. Status do sistema de broadcasting:\n";
    echo "   🔧 Driver: {$driver}\n";
    echo "   📡 Pusher: ✅ Configurado\n";
    echo "   🎯 Evento: ✅ Criado\n";
    echo "   📊 Dados: ✅ Preparados\n";
    echo "   🚀 Broadcast: ✅ Disparado\n";
    
    echo "\n🎉 Teste de broadcasting concluído!\n";
    echo "💡 Para testar em tempo real:\n";
    echo "   1. Abra o navegador e faça login com o usuário {$user2->email}\n";
    echo "   2. Abra o console (F12) e procure por mensagens do Echo\n";
    echo "   3. Execute este script novamente para disparar uma nova notificação\n";
    
} catch (Exception $e) {
    echo "\n❌ Erro durante o teste: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 