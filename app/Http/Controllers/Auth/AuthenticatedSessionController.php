<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\UserLoginNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Enviar email de notificação de login
        $user = Auth::user();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        try {
            $user->notify(new UserLoginNotification($user, $ipAddress, $userAgent));
            
            // Adicionar mensagem de sucesso para o snackbar
            session()->flash('email_sent', [
                'type' => 'success',
                'title' => 'Notificação de Login Enviada',
                'message' => "Notificação de login enviada para {$user->email}"
            ]);
            
        } catch (\Exception $e) {
            // Log do erro mas não interrompe o login
            Log::error('Erro ao enviar notificação de login: ' . $e->getMessage());
            
            // Adicionar mensagem de erro para o snackbar
            session()->flash('email_error', [
                'type' => 'error',
                'title' => 'Erro na Notificação de Login',
                'message' => 'Não foi possível enviar a notificação de login'
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
