<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid','profile','email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            // pega dados do Google (state é verificado via sessão)
            $googleUser = Socialite::driver('google')->stateless(false)->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(str()->random(32)),
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user, remember: true);

            // Enviar notificação de login por email
            try {
                $ipAddress = request()->ip();
                $userAgent = request()->userAgent();
                
                $user->notify(new UserLoginNotification($user, $ipAddress, $userAgent));
                
                Log::info('📧 Notificação de login via Google enviada com sucesso', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'ip_address' => $ipAddress
                ]);
                
            } catch (\Exception $e) {
                Log::error('❌ Erro ao enviar notificação de login via Google', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->intended('/pt/dashboard');
        } catch (\Exception $e) {
            \Log::error('Erro no callback do Google', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect('/pt/login')->withErrors(['google_error' => 'Erro na autenticação com Google.']);
        }
    }
}