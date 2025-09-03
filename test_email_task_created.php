<?php

/**
 * 🧪 Teste Específico - Email de Tarefa Criada
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;

echo "🧪 Teste Específico - Email de Tarefa Criada...\n\n";

try {
    // Buscar usuário real
    echo "1️⃣ Buscando usuário...\n";
    $realUser = User::where('email', 'tdrummontt@gmail.com')->first();
    
    if (!$realUser) {
        echo "   ❌ Usuário não encontrado!\n";
        exit(1);
    }
    
    echo "   ✅ Usuário: {$realUser->name} ({$realUser->email})\n\n";
    
    // Criar tarefa idêntica à que você criou
    echo "2️⃣ Criando tarefa idêntica...\n";
    
    $task = Task::create([
        'title' => 'novo teste - VERIFICAÇÃO',
        'description' => 'faz isso funcionar loho - TESTE DE EMAIL',
        'status' => 'pending',
        'priority' => 'medium',
        'category' => 'Desenvolvimento',
        'estimated_hours' => 24,
        'due_date' => '2025-09-04',
        'start_date' => '2025-09-03',
        'created_by' => $realUser->id,
        'assigned_to' => $realUser->id,
        'order' => 1
    ]);
    
    echo "   ✅ Tarefa criada (ID: {$task->id})\n";
    echo "   📝 Título: {$task->title}\n";
    echo "   📋 Descrição: {$task->description}\n";
    echo "   🔴 Prioridade: {$task->priority}\n";
    echo "   ⏳ Status: {$task->status}\n\n";
    
    // Enviar notificação
    echo "3️⃣ Enviando notificação...\n";
    
    try {
        $realUser->notify(new TaskCreatedNotification($task, $realUser));
        echo "   ✅ Notificação enviada com sucesso!\n";
        echo "   📧 Assunto: 🎯 Nova Tarefa Criada: {$task->title}\n";
        echo "   📧 Para: {$realUser->email}\n";
        echo "   📧 Data/Hora: " . now()->format('d/m/Y H:i:s') . "\n";
        
        // Verificar se foi salva no banco
        $recentNotification = $realUser->notifications()->latest()->first();
        if ($recentNotification) {
            echo "   ✅ Notificação salva no banco (ID: {$recentNotification->id})\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao enviar notificação: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Teste concluído!\n";
    echo "📧 Verifique seu email agora!\n";
    echo "🔍 Procure por: 🎯 Nova Tarefa Criada: novo teste - VERIFICAÇÃO\n";
    
    // Limpar tarefa de teste
    $task->delete();
    echo "🧹 Tarefa de teste removida\n";
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
