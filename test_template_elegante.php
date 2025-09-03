<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎨 Testando template elegante...\n\n";

try {
    // Dados de teste para o template
    $task = (object)[
        'title' => 'Tarefa Teste Elegante',
        'description' => 'Esta é uma tarefa de teste para verificar o novo design elegante dos emails!',
        'priority' => 'high',
        'status' => 'pending',
        'due_date' => now()->addDays(7),
        'tags' => 'teste, elegante, design'
    ];
    
    $assignedTo = (object)['name' => 'Usuário Teste'];
    $assignedBy = (object)['name' => 'Admin Teste'];
    $taskUrl = 'http://localhost:8000/tasks/1';
    
    // Enviar email com template elegante
    Mail::send('emails.tasks.assigned', [
        'task' => $task,
        'assignedTo' => $assignedTo,
        'assignedBy' => $assignedBy,
        'taskUrl' => $taskUrl
    ], function($message) {
        $message->to('tdrummontt@gmail.com')
                ->subject('🎯 Nova Tarefa Elegante - ' . date('H:i:s'));
    });
    
    echo "✅ Email elegante enviado com sucesso!\n";
    echo "📧 Verifique sua caixa de entrada: tdrummontt@gmail.com\n";
    echo "🎨 Template usado: emails.tasks.assigned\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao enviar email elegante:\n";
    echo "   " . $e->getMessage() . "\n\n";
}

echo "\n🚀 Teste de template elegante concluído!\n";
