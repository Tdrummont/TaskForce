<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Certifique-se de ter um modelo User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtém as informações do usuário do Google e loga/registra o usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Verifica se o usuário já existe no nosso banco de dados
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Se o usuário existe, faça o login
                Auth::login($user);
                return redirect()->intended('/dashboard'); // Redireciona para o dashboard
            } else {
                // Se o usuário não existe, crie um novo registro
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Senha aleatória para usuários do Google
                ]);

                Auth::login($newUser);
                return redirect()->intended('/dashboard');
            }

        } catch (\Exception $e) {
            // Lidar com erros de autenticação
            // dd($e->getMessage()); // Descomente para depurar o erro
            return redirect('/login')->withErrors(['google_error' => 'Não foi possível fazer login com o Google. Tente novamente.']);
        }
    }
}