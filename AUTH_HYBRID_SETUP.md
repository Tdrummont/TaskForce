# Sistema de Autenticação Híbrido - Laravel + JWT + Sessões

## Visão Geral

Este projeto implementa um sistema de autenticação híbrido que suporta tanto **autenticação por sessões** (para aplicações web tradicionais) quanto **autenticação por JWT** (para APIs e SPAs).

## Configuração Implementada

### 1. Guards de Autenticação (`config/auth.php`)

```php
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

### 2. Broadcasting com Autenticação Dupla (`app/Providers/AppServiceProvider.php`)

```php
// Habilitar broadcasting para canais privados com autenticação híbrida (web + api)
Broadcast::routes(['middleware' => ['auth:web,api']]);
```

### 3. Rotas API (`routes/api.php`)

Rotas protegidas que requerem JWT:
- `/api/login` - Login via API
- `/api/user` - Dados do usuário autenticado
- `/api/tasks` - CRUD de tarefas
- `/api/dashboard` - Dados do dashboard

### 4. Canais de Broadcasting (`routes/channels.php`)

```php
// Canal privado para usuários específicos
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para tarefas específicas
Broadcast::channel('task.{id}', function ($user, $id) {
    $task = \App\Models\Task::find($id);
    return $task && ($task->user_id === $user->id || $user->hasRole('admin'));
});
```

## Como Funciona

### Frontend Web (Sessões)

1. **Login via Breeze**: Usuário faz login normalmente na interface web
2. **Cookie de Sessão**: Laravel cria um cookie de sessão
3. **Laravel Echo**: Automaticamente envia o cookie para `/broadcasting/auth`
4. **Autenticação**: Laravel autentica via guard `web` (sessão)

### Frontend API (JWT)

1. **Login via API**: Usuário faz POST para `/api/login` com credenciais
2. **Token JWT**: Laravel retorna um token JWT
3. **Armazenamento**: Token é salvo no `localStorage` ou `sessionStorage`
4. **Laravel Echo**: Envia o token no header `Authorization: Bearer {token}`
5. **Autenticação**: Laravel autentica via guard `api` (JWT)

## Configuração do Frontend

### Para Aplicações Web (Sessões)

```javascript
// Não precisa de configuração extra
// Laravel Echo automaticamente usa cookies de sessão
```

### Para Aplicações API (JWT)

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const token = localStorage.getItem('jwt_token');

export const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: `${import.meta.env.VITE_API_URL}/broadcasting/auth`,
    auth: {
        headers: {
            Authorization: token ? `Bearer ${token}` : '',
            Accept: 'application/json',
        },
    },
});
```

## Componente Vue Atualizado

O componente `RealTimeNotifications.vue` foi atualizado para suportar ambos os tipos de autenticação:

```javascript
// Verificar se há token JWT ou usar sessão
const jwtToken = localStorage.getItem('jwt_token') || sessionStorage.getItem('jwt_token');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Preparar headers baseados no tipo de autenticação
const headers = {
    'Content-Type': 'application/json',
};

if (jwtToken) {
    // Autenticação via JWT (API)
    headers['Authorization'] = `Bearer ${jwtToken}`;
    headers['Accept'] = 'application/json';
} else if (csrfToken) {
    // Autenticação via sessão (Web)
    headers['X-CSRF-TOKEN'] = csrfToken;
}
```

## Testes

### Teste 1: Usuário Web (Breeze)

1. Acesse a aplicação web
2. Faça login normalmente
3. Acesse uma página com notificações em tempo real
4. Verifique no console: "✅ Canal autenticado com sucesso"
5. Status da conexão deve mostrar "Conectado"

### Teste 2: Usuário API (JWT)

1. Faça POST para `/api/login` com credenciais válidas
2. Guarde o token JWT retornado
3. Configure o Laravel Echo com o token
4. Verifique no console: "✅ Canal autenticado com sucesso"
5. Status da conexão deve mostrar "Conectado"

## Solução de Problemas

### Erro 403 Forbidden em `/broadcasting/auth`

**Causa**: Laravel não consegue autenticar o usuário

**Soluções**:
1. Verificar se o middleware está configurado corretamente: `['auth:web,api']`
2. Verificar se o guard `api` está configurado para usar JWT
3. Verificar se o token JWT é válido e não expirou
4. Verificar se a sessão web não expirou

### Canal não se conecta

**Causas possíveis**:
1. Variáveis do Pusher não configuradas no `.env`
2. Token JWT inválido ou expirado
3. Sessão web expirada
4. Problemas de CORS

**Soluções**:
1. Verificar configuração do Pusher no `.env`
2. Verificar validade do token JWT
3. Verificar se a sessão web está ativa
4. Verificar logs do Laravel para erros de autenticação

## Variáveis de Ambiente Necessárias

```env
# Pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# JWT
JWT_SECRET=your_jwt_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Broadcasting
BROADCAST_DRIVER=pusher
```

## Benefícios do Sistema Híbrido

1. **Flexibilidade**: Suporta tanto aplicações web quanto APIs
2. **Escalabilidade**: Pode servir diferentes tipos de clientes
3. **Manutenibilidade**: Código único para ambos os tipos de autenticação
4. **Segurança**: Cada tipo de autenticação usa o método mais apropriado
5. **Performance**: Sessões para aplicações web, JWT para APIs

## Próximos Passos

1. Implementar métodos `apiLogin`, `apiStore`, etc. nos controllers
2. Adicionar middleware de rate limiting para APIs
3. Implementar refresh tokens para JWT
4. Adicionar validação de entrada para APIs
5. Implementar testes automatizados para ambos os tipos de autenticação 