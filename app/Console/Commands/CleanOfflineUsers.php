<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Events\UserOnlineStatus;

class CleanOfflineUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:clean-offline-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove users who are no longer online and trigger offline events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Limpando usuários offline...');
        
        $users = User::all();
        $offlineUsers = [];
        
        foreach ($users as $user) {
            $cacheKey = "user_online_{$user->id}";
            
            // Se o cache expirou, o usuário está offline
            if (!Cache::has($cacheKey)) {
                $offlineUsers[] = $user;
            }
        }
        
        if (empty($offlineUsers)) {
            $this->info('✅ Todos os usuários estão online');
            return;
        }
        
        $this->info("📤 Disparando eventos de status offline para " . count($offlineUsers) . " usuários...");
        
        foreach ($offlineUsers as $user) {
            try {
                event(new UserOnlineStatus($user, 'offline'));
                $this->line("👋 {$user->name} está offline");
            } catch (\Exception $e) {
                $this->error("❌ Erro ao disparar evento para {$user->name}: " . $e->getMessage());
            }
        }
        
        $this->info('✅ Limpeza de usuários offline concluída');
    }
}
