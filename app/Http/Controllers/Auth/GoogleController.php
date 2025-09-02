<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Log para debug
            Log::info('Google callback iniciado', ['request' => $request->all()]);
            
            // Verificar se temos o código de autorização
            if (!$request->has('code')) {
                throw new \Exception('Código de autorização não encontrado');
            }
            
            // Obter usuário do Google
            $googleUser = Socialite::driver('google')->user();
            
            // Log dos dados do usuário Google
            Log::info('Dados do usuário Google', [
                'id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            // Verificar se o usuário já existe
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Criar novo usuário
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)), // Senha aleatória
                    'email_verified_at' => now(), // Email já verificado pelo Google
                    'google_id' => $googleUser->id,
                ]);
                
                Log::info('Novo usuário criado', ['user_id' => $user->id]);
            } else {
                // Atualizar google_id se não existir
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                    Log::info('Google ID atualizado para usuário existente', ['user_id' => $user->id]);
                }
            }

            // Gerar token JWT
            $token = JWTAuth::fromUser($user);
            Log::info('Token JWT gerado', ['user_id' => $user->id]);

            // Fazer login do usuário
            Auth::login($user);

            // Retornar resposta com token
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no Google callback', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer login com Google: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer logout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao renovar token: ' . $e->getMessage()
            ], 500);
        }
    }
} 