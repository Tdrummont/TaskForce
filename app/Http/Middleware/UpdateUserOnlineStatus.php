<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\UserOnlineStatus;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = "user_online_{$user->id}";
            
            // Verificar se o usuário estava offline
            $wasOffline = !Cache::has($cacheKey);
            
            // Marcar usuário como online por 5 minutos
            Cache::put($cacheKey, true, now()->addMinutes(5));
            
            \Log::info('Middleware UpdateUserOnlineStatus executado', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'was_offline' => $wasOffline,
                'cache_key' => $cacheKey
            ]);
            
            // Se o usuário estava offline, disparar evento de status online
            if ($wasOffline) {
                try {
                    event(new UserOnlineStatus($user, 'online'));
                    \Log::info('Evento UserOnlineStatus disparado', [
                        'user_id' => $user->id,
                        'status' => 'online'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao disparar evento de status online', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $next($request);
    }
}
