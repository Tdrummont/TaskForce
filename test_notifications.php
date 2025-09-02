<?php

/**
 * 🧪 Script de Teste para Sistema de Notificações
 * 
 * Este script testa o sistema de notificações do Laravel
 * Execute: php test_notifications.php
 */

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Log;

echo "🧪 Testando Sistema de Notificações...\n\n";

try {
    // Teste 1: Verificar se as classes de notificação existem
    echo "1️⃣ Verificando classes de notificação...\n";
    
    if (class_exists('App\Notifications\TaskAssignedNotification')) {
        echo "   ✅ TaskAssignedNotification existe\n";
    } else {
        echo "   ❌ TaskAssignedNotification não encontrada\n";
    }
    
    if (class_exists('App\Notifications\UserLoginNotification')) {
        echo "   ✅ UserLoginNotification existe\n";
    } else {
        echo "   ❌ UserLoginNotification não encontrada\n";
    }
    
    // Teste 2: Verificar se o modelo User tem o trait Notifiable
    echo "\n2️⃣ Verificando modelo User...\n";
    
    $user = User::first();
    if ($user) {
        echo "   ✅ Usuário encontrado: {$user->name} ({$user->email})\n";
        
        if (method_exists($user, 'notify')) {
            echo "   ✅ Método notify() disponível\n";
        } else {
            echo "   ❌ Método notify() não disponível\n";
        }
        
        if (method_exists($user, 'notifications')) {
            echo "   ✅ Relacionamento notifications() disponível\n";
        } else {
            echo "   ❌ Relacionamento notifications() não disponível\n";
        }
        
    } else {
        echo "   ❌ Nenhum usuário encontrado no banco\n";
        exit(1);
    }
    
    // Teste 3: Verificar tabela de notificações
    echo "\n3️⃣ Verificando tabela de notificações...\n";
    
    try {
        $notificationsCount = $user->notifications()->count();
        echo "   ✅ Tabela de notificações acessível\n";
        echo "   📊 Notificações existentes: {$notificationsCount}\n";
    } catch (Exception $e) {
        echo "   ❌ Erro ao acessar tabela de notificações: " . $e->getMessage() . "\n";
    }
    
    // Teste 4: Testar notificação de login
    echo "\n4️⃣ Testando notificação de login...\n";
    
    try {
        $user->notify(new UserLoginNotification($user, '127.0.0.1', 'Test Browser'));
        echo "   ✅ Notificação de login criada com sucesso\n";
        
        // Verificar se foi salva no banco
        $recentNotification = $user->notifications()->latest()->first();
        if ($recentNotification) {
            echo "   ✅ Notificação salva no banco (ID: {$recentNotification->id})\n";
            echo "   📝 Tipo: {$recentNotification->type}\n";
            echo "   📅 Criada em: {$recentNotification->created_at}\n";
        } else {
            echo "   ❌ Notificação não foi salva no banco\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erro ao criar notificação de login: " . $e->getMessage() . "\n";
    }
    
    // Teste 5: Testar notificação de tarefa (se houver tarefas)
    echo "\n5️⃣ Testando notificação de tarefa...\n";
    
    $task = $user->tasks()->first();
    if ($task) {
        try {
            $user->notify(new TaskAssignedNotification($task, $user, $user));
            echo "   ✅ Notificação de tarefa criada com sucesso\n";
            
            // Verificar se foi salva no banco
            $recentTaskNotification = $user->notifications()->latest()->first();
            if ($recentTaskNotification && $recentTaskNotification->type === 'App\Notifications\TaskAssignedNotification') {
                echo "   ✅ Notificação de tarefa salva no banco (ID: {$recentTaskNotification->id})\n";
            } else {
                echo "   ❌ Notificação de tarefa não foi salva corretamente\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Erro ao criar notificação de tarefa: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠️ Nenhuma tarefa encontrada para testar\n";
    }
    
    // Teste 6: Verificar configuração de email
    echo "\n6️⃣ Verificando configuração de email...\n";
    
    $mailConfig = config('mail');
    echo "   - Mailer padrão: " . $mailConfig['default'] . "\n";
    echo "   - Host SMTP: " . $mailConfig['mailers']['smtp']['host'] . "\n";
    echo "   - Porta: " . $mailConfig['mailers']['smtp']['port'] . "\n";
    echo "   - Username: " . $mailConfig['mailers']['smtp']['username'] . "\n";
    echo "   - From Address: " . $mailConfig['from']['address'] . "\n";
    
    echo "\n🎉 Teste de notificações concluído!\n\n";
    
    echo "💡 Próximos passos:\n";
    echo "1. Configure a senha de aplicativo no .env\n";
    echo "2. Execute: php artisan config:clear\n";
    echo "3. Teste o sistema web\n";
    echo "4. Verifique se as notificações chegam por email\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
    echo "📋 Verificações:\n";
    echo "1. Execute: php artisan migrate\n";
    echo "2. Verifique se o banco está configurado\n";
    echo "3. Confirme se as classes de notificação existem\n";
}

echo "\n🔍 Logs disponíveis em: storage/logs/laravel.log\n";
echo "📧 Para testar emails, configure a senha de aplicativo primeiro!\n"; 