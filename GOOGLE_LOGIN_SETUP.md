# 🔐 Login com Google - Setup Completo

## 📋 Visão Geral

Este documento contém o passo a passo completo para implementar login com Google no projeto Laravel + Vue.js, incluindo JWT para autenticação de API.

## 🚀 Passo a Passo Completo

### 1. Configuração do Google Cloud Console

#### 1.1 Acessar Google Cloud Console
1. Acesse: https://console.cloud.google.com/
2. Faça login com sua conta Google
3. Crie um novo projeto ou selecione um existente

#### 1.2 Habilitar APIs
1. No menu lateral, vá em "APIs e Serviços" > "Biblioteca"
2. Procure e habilite:
   - "Google+ API" ou "Google Identity"
   - "Google+ Domains API"

#### 1.3 Criar Credenciais OAuth 2.0
1. Vá em "APIs e Serviços" > "Credenciais"
2. Clique em "Criar Credenciais" > "ID do Cliente OAuth 2.0"
3. Configure:
   - **Tipo de aplicativo**: Aplicativo da Web
   - **Nome**: TaskForce Login
   - **URIs de redirecionamento autorizados**:
     - `http://localhost:8000/auth/google/callback` (desenvolvimento)
     - `https://seudominio.com/auth/google/callback` (produção)

#### 1.4 Obter Credenciais
Após criar, você receberá:
- **ID do Cliente**: `123456789-abcdefghijklmnop.apps.googleusercontent.com`
- **Segredo do Cliente**: `GOCSPX-abcdefghijklmnopqrstuvwxyz`

### 2. Configuração do Laravel

#### 2.1 Instalar Dependências
```bash
composer require laravel/socialite
composer require php-open-source-saver/jwt-auth
```

#### 2.2 Configurar .env
Adicione ao seu arquivo `.env`:
```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=seu_client_id_aqui
GOOGLE_CLIENT_SECRET=seu_client_secret_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# JWT Configuration
JWT_SECRET=seu_jwt_secret_aqui
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

#### 2.3 Configurar services.php
```php
// config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

#### 2.4 Configurar auth.php
```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

#### 2.5 Publicar Configurações JWT
```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

#### 2.6 Criar Migration para google_id
```bash
php artisan make:migration add_google_id_to_users_table
```

Conteúdo da migration:
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('google_id');
    });
}
```

#### 2.7 Executar Migration
```bash
php artisan migrate
```

### 3. Configuração do Model User

#### 3.1 Atualizar User.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

### 4. Controller de Autenticação Google

#### 4.1 Criar GoogleController
```bash
php artisan make:controller Auth/GoogleController
```

#### 4.2 Implementar Métodos
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->id,
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            $token = JWTAuth::fromUser($user);
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer login com Google: ' . $e->getMessage()
            ], 500);
        }
    }

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
```

### 5. Configuração das Rotas

#### 5.1 Adicionar Rotas no web.php
```php
use App\Http\Controllers\Auth\GoogleController;

// Rotas de autenticação Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/auth/logout', [GoogleController::class, 'logout'])->name('google.logout');
Route::post('/auth/refresh', [GoogleController::class, 'refresh'])->name('google.refresh');

// Página de callback do Google (frontend)
Route::get('/auth/google/callback-page', function () {
    return Inertia::render('Auth/GoogleCallback');
})->name('google.callback.page');
```

### 6. Frontend Vue.js

#### 6.1 Componente GoogleLoginButton.vue
```vue
<template>
    <div class="google-login-container">
        <button
            @click="loginWithGoogle"
            :disabled="isLoading"
            class="google-login-btn"
        >
            <svg v-if="!isLoading" class="google-icon" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span class="btn-text">
                {{ isLoading ? 'Entrando...' : 'Entrar com Google' }}
            </span>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const isLoading = ref(false);

const loginWithGoogle = async () => {
    try {
        isLoading.value = true;
        window.location.href = route('google.redirect');
    } catch (error) {
        console.error('Erro ao iniciar login com Google:', error);
        isLoading.value = false;
        alert('Erro ao iniciar login com Google. Tente novamente.');
    }
};
</script>

<style scoped>
.google-login-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    max-width: 300px;
    padding: 12px 24px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.google-login-btn:hover:not(:disabled) {
    background: #f9fafb;
    border-color: #d1d5db;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.google-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}
</style>
```

#### 6.2 Página de Callback GoogleCallback.vue
```vue
<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div v-if="isLoading" class="text-center">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Processando Login</h2>
                <p class="text-gray-600">Aguarde enquanto finalizamos sua autenticação...</p>
            </div>

            <div v-else-if="isSuccess" class="text-center">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Login Realizado!</h2>
                <p class="text-gray-600">Redirecionando para o dashboard...</p>
            </div>

            <div v-else-if="error" class="text-center">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Erro no Login</h2>
                <p class="text-gray-600 mb-6">{{ error }}</p>
                <button @click="retryLogin" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">
                    Tentar Novamente
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(true);
const isSuccess = ref(false);
const error = ref(null);

onMounted(async () => {
    await processCallback();
});

const processCallback = async () => {
    try {
        const response = await fetch('/auth/google/callback', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            localStorage.setItem('jwt_token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));

            isLoading.value = false;
            isSuccess.value = true;

            setTimeout(() => {
                router.visit('/dashboard');
            }, 2000);
        } else {
            throw new Error(data.message || 'Erro ao processar login');
        }
    } catch (err) {
        isLoading.value = false;
        error.value = err.message || 'Erro inesperado ao processar login';
    }
};

const retryLogin = () => {
    isLoading.value = true;
    error.value = null;
    processCallback();
};
</script>
```

#### 6.3 Atualizar Login.vue
```vue
<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- ... código existente ... -->
        
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form @submit.prevent="submit">
                <!-- ... formulário existente ... -->
            </form>

            <!-- Separador -->
            <div class="mt-6 flex items-center">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-3 text-sm text-gray-500">ou</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Botão de Login com Google -->
            <div class="mt-6">
                <GoogleLoginButton />
            </div>

            <!-- ... resto do código ... -->
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GoogleLoginButton from '@/Components/GoogleLoginButton.vue';

// ... resto do código ...
</script>
```

## 🔄 Fluxo Completo

### 1. Usuário clica em "Entrar com Google"
### 2. Redirecionamento para Google OAuth
### 3. Usuário autoriza o aplicativo
### 4. Google redireciona para `/auth/google/callback`
### 5. Laravel processa dados e cria/atualiza usuário
### 6. Gera token JWT
### 7. Retorna resposta com token
### 8. Frontend armazena token no localStorage
### 9. Redireciona para dashboard

## 🛠️ Como Usar

### 1. Configurar Credenciais
```bash
# Editar .env
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 2. Executar Migrations
```bash
php artisan migrate
```

### 3. Testar
```bash
php artisan serve
```

Acesse `http://localhost:8000/login` e clique em "Entrar com Google"

## 🔒 Segurança

- ✅ Credenciais seguras no .env
- ✅ JWT para autenticação de API
- ✅ Validação de email do Google
- ✅ Tokens com expiração
- ✅ Refresh automático de tokens

## 🚀 Próximos Passos

1. Implementar logout com JWT
2. Adicionar middleware de autenticação JWT
3. Implementar refresh automático de tokens
4. Adicionar validação de domínio de email
5. Implementar rate limiting
6. Adicionar logs de autenticação

## 📝 Notas Importantes

- Mantenha as credenciais do Google seguras
- Nunca commite o .env no Git
- Use HTTPS em produção
- Configure corretamente os URIs de redirecionamento
- Teste o fluxo completo antes de ir para produção 