<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Certifique-se de ter um modelo User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtém as informações do usuário do Google e loga/registra o usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('=== CALLBACK GOOGLE INICIADO ===', [
                'url' => $request->fullUrl(),
                'code' => $request->get('code'),
                'state' => $request->get('state'),
                'error' => $request->get('error')
            ]);

            if ($request->has('error')) {
                Log::error('Erro do Google OAuth', ['error' => $request->get('error')]);
                return redirect('/pt/login')->withErrors(['google_error' => 'Autorização negada pelo Google.']);
            }

            $googleUser = Socialite::driver('google')->user();
            Log::info('Usuário Google obtido', ['user' => $googleUser->email]);

            // Verifica se o usuário já existe no nosso banco de dados
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Verifica se já existe usuário com mesmo email
                $user = User::where('email', $googleUser->email)->first();
                if ($user) {
                    // Atualiza o google_id do usuário existente
                    $user->update(['google_id' => $googleUser->id]);
                    Log::info('Google ID adicionado ao usuário existente', ['user_id' => $user->id]);
                } else {
                    // Cria novo usuário
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);
                    Log::info('Novo usuário criado', ['user_id' => $user->id]);
                }
            }

            Auth::login($user);
            Log::info('Usuário logado com sucesso', ['user_id' => $user->id]);

            // Redireciona para o dashboard usando a rota localizada
            return redirect('/pt/dashboard');

        } catch (\Exception $e) {
            Log::error('Erro no callback do Google', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('/pt/login')->withErrors(['google_error' => 'Erro na autenticação com Google.']);
        }
    }
}