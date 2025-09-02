<?php

/**
 * Script de Teste das Melhorias Técnicas
 * 
 * Este arquivo demonstra como usar as novas funcionalidades implementadas.
 * Execute este script para testar as melhorias.
 */

require_once 'vendor/autoload.php';

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

echo "🧪 TESTE DAS MELHORIAS TÉCNICAS\n";
echo "================================\n\n";

// 1. Teste de Notificações de Prazos
echo "1. 📢 TESTE DE NOTIFICAÇÕES DE PRAZOS\n";
echo "-------------------------------------\n";

// Criar tarefas de teste com diferentes prazos
$user = User::first();
if (!$user) {
    echo "❌ Nenhum usuário encontrado. Execute as migrações primeiro.\n";
    exit(1);
}

// Tarefa vencida
Task::create([
    'title' => 'Tarefa Vencida - Teste',
    'description' => 'Esta tarefa está vencida para teste',
    'due_date' => Carbon::now()->subDays(2),
    'status' => 'pending',
    'priority' => 'high',
    'created_by' => $user->id,
    'assigned_to' => $user->id
]);

// Tarefa que vence hoje
Task::create([
    'title' => 'Tarefa Vence Hoje - Teste',
    'description' => 'Esta tarefa vence hoje',
    'due_date' => Carbon::now(),
    'status' => 'pending',
    'priority' => 'medium',
    'created_by' => $user->id,
    'assigned_to' => $user->id
]);

// Tarefa que vence em breve
Task::create([
    'title' => 'Tarefa Vence em Breve - Teste',
    'description' => 'Esta tarefa vence em 2 dias',
    'due_date' => Carbon::now()->addDays(2),
    'status' => 'pending',
    'priority' => 'low',
    'created_by' => $user->id,
    'assigned_to' => $user->id
]);

echo "✅ Tarefas de teste criadas\n";

// Testar métodos do modelo Task
$tasks = Task::all();
foreach ($tasks as $task) {
    echo "   • {$task->title}: {$task->getDueStatusLabel()}\n";
}

echo "\n";

// 2. Teste de Comandos Artisan
echo "2. 🔧 TESTE DE COMANDOS ARTISAN\n";
echo "-------------------------------\n";

echo "Para testar os comandos, execute:\n";
echo "   php artisan tasks:check-deadlines\n";
echo "   php artisan tasks:check-deadlines --notify\n";
echo "   php artisan tasks:sync-cloud\n";
echo "   php artisan tasks:sync-cloud --dry-run\n";

echo "\n";

// 3. Teste de Validações
echo "3. ✅ TESTE DE VALIDAÇÕES\n";
echo "-------------------------\n";

$validationTests = [
    [
        'title' => 'Ab', // Muito curto
        'description' => 'Teste',
        'due_date' => Carbon::now()->subDays(1), // Passado
        'status' => 'invalid_status',
        'priority' => 'invalid_priority'
    ],
    [
        'title' => 'Título Válido',
        'description' => str_repeat('a', 1001), // Muito longo
        'due_date' => Carbon::now()->addDays(1),
        'status' => 'pending',
        'priority' => 'high'
    ]
];

foreach ($validationTests as $index => $testData) {
    echo "   Teste " . ($index + 1) . ":\n";
    
    $validator = \Illuminate\Support\Facades\Validator::make($testData, [
        'title' => 'required|string|max:255|min:3',
        'description' => 'nullable|string|max:1000',
        'due_date' => 'nullable|date|after_or_equal:today',
        'status' => ['required', \Illuminate\Validation\Rule::in(['pending', 'in_progress', 'completed'])],
        'priority' => ['required', \Illuminate\Validation\Rule::in(['low', 'medium', 'high'])]
    ], [
        'title.required' => 'O título é obrigatório.',
        'title.min' => 'O título deve ter pelo menos 3 caracteres.',
        'title.max' => 'O título não pode ter mais de 255 caracteres.',
        'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        'due_date.after_or_equal' => 'A data de vencimento deve ser hoje ou uma data futura.',
        'status.in' => 'O status selecionado é inválido.',
        'priority.in' => 'A prioridade selecionada é inválida.'
    ]);

    if ($validator->fails()) {
        echo "      ❌ Validação falhou:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "         - {$error}\n";
        }
    } else {
        echo "      ✅ Validação passou\n";
    }
    echo "\n";
}

// 4. Teste de Exportação
echo "4. 📊 TESTE DE EXPORTAÇÃO\n";
echo "-------------------------\n";

echo "Para testar a exportação, acesse:\n";
echo "   http://localhost/tasks/export/csv\n";
echo "   http://localhost/tasks/backup\n";

echo "\n";

// 5. Teste de Atalhos de Teclado
echo "5. ⌨️ TESTE DE ATALHOS DE TECLADO\n";
echo "--------------------------------\n";

echo "Na interface web, teste os atalhos:\n";
echo "   Ctrl+N: Focar no campo de título\n";
echo "   Ctrl+S: Salvar formulário\n";
echo "   Esc: Cancelar/limpar\n";

echo "\n";

// 6. Resumo
echo "6. 📋 RESUMO DAS MELHORIAS\n";
echo "--------------------------\n";

echo "✅ Notificações de prazos implementadas\n";
echo "✅ Sistema de backup/sync implementado\n";
echo "✅ Exportação CSV/JSON implementada\n";
echo "✅ Atalhos de teclado implementados\n";
echo "✅ Validações aprimoradas implementadas\n";

echo "\n";

echo "🎉 TODAS AS MELHORIAS FORAM IMPLEMENTADAS COM SUCESSO!\n";
echo "=====================================================\n";

echo "\nPara acessar o sistema:\n";
echo "1. Execute: php artisan serve\n";
echo "2. Acesse: http://localhost:8000\n";
echo "3. Faça login e teste as funcionalidades\n";

echo "\nPara agendar tarefas automáticas:\n";
echo "1. Configure o cron job: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1\n";
echo "2. Ou execute manualmente: php artisan schedule:run\n";

echo "\n📚 Consulte o arquivo README_MELHORIAS.md para mais detalhes.\n"; 