<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestNotificationAPI extends Command
{
    protected $signature = 'test:notification-api {--user-id=2 : ID do usuário para testar}';
    protected $description = 'Testa a API de notificações simulando uma requisição real';

    public function handle()
    {
        $userId = $this->option('user-id');
        
        $this->info('🧪 Testando API de Notificações...');
        $this->newLine();

        // Encontrar o usuário
        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ Usuário com ID {$userId} não encontrado");
            return 1;
        }

        $this->info("👤 Testando para usuário: {$user->name} (ID: {$user->id})");
        $this->newLine();

        // Simular autenticação
        Auth::login($user);

        // Criar uma requisição simulada
        $request = new Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Testar o controller
        $controller = new NotificationController();

        try {
            $this->info('📡 Chamando NotificationController::apiIndex()...');
            $response = $controller->apiIndex($request);
            
            $this->info('✅ Resposta recebida com sucesso!');
            
            // Decodificar a resposta
            $data = json_decode($response->getContent(), true);
            
            $this->info('📊 Estrutura da resposta:');
            $this->line("   success: " . ($data['success'] ? 'true' : 'false'));
            $this->line("   notifications count: " . count($data['notifications'] ?? []));
            
            if (isset($data['notifications']) && count($data['notifications']) > 0) {
                $this->newLine();
                $this->info('📝 Notificações encontradas:');
                
                foreach (array_slice($data['notifications'], 0, 3) as $index => $notification) {
                    $this->line("   " . ($index + 1) . ". ID: {$notification['id']}");
                    $this->line("      Título: " . ($notification['title'] ?? 'SEM TÍTULO'));
                    $this->line("      Mensagem: " . ($notification['message'] ?? 'SEM MENSAGEM'));
                    $this->line("      Tipo: " . ($notification['type'] ?? 'SEM TIPO'));
                    $this->line("      Criada em: " . ($notification['created_at'] ?? 'SEM DATA'));
                    $this->line("      Lida: " . ($notification['read_at'] ? 'Sim' : 'Não'));
                    $this->line("      ---");
                }
                
                $this->newLine();
                $this->info('🎯 Análise dos Dados:');
                
                $withTitle = array_filter($data['notifications'], function($n) {
                    return isset($n['title']) && !empty($n['title']);
                });
                
                $this->line("   📊 Notificações com título: " . count($withTitle) . "/" . count($data['notifications']));
                
                $delegationNotifications = array_filter($data['notifications'], function($n) {
                    return isset($n['type']) && $n['type'] === 'task_delegated';
                });
                
                $this->line("   🔄 Notificações de delegação: " . count($delegationNotifications));
                
                if (count($delegationNotifications) > 0) {
                    $this->info('✅ Notificações de delegação encontradas!');
                    foreach ($delegationNotifications as $delegation) {
                        $this->line("      📝 {$delegation['title']} - {$delegation['message']}");
                    }
                } else {
                    $this->warn('⚠️ Nenhuma notificação de delegação encontrada');
                }
                
            } else {
                $this->warn('⚠️ Nenhuma notificação encontrada na resposta da API');
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao testar API: {$e->getMessage()}");
            $this->error("📋 Stack trace: {$e->getTraceAsString()}");
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Teste da API concluído!');
        
        $this->newLine();
        $this->info('💡 Para testar no frontend:');
        $this->line('1. Faça login com o usuário: ' . $user->email);
        $this->line('2. Abra o console do navegador (F12)');
        $this->line('3. Clique no ícone de notificações');
        $this->line('4. Verifique se há logs no console');
        $this->line('5. Verifique se as notificações aparecem');

        return 0;
    }
} 