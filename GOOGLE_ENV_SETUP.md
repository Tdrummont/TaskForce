# Configuração das Variáveis de Ambiente do Google OAuth

## Arquivo .env

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=173401741666-l54s12rclh9e60up0feugvsl4dhacgme.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Configuração no Google Cloud Console

1. **Acesse**: https://console.cloud.google.com/
2. **Projeto**: Selecione ou crie um projeto
3. **APIs**: Habilite a "Google People API"
4. **Credenciais**: Crie um ID do cliente OAuth 2.0
5. **Tipo**: Aplicativo da Web
6. **URIs de Redirecionamento**: 
   - `http://localhost:8000/auth/google/callback`
   - `http://127.0.0.1:8000/auth/google/callback`

## Teste da Integração

1. **Inicie o servidor**: `php artisan serve`
2. **Acesse**: http://localhost:8000/login
3. **Clique no botão**: "Entrar com Google"
4. **Faça login**: Com sua conta Google
5. **Verifique**: Se foi redirecionado para o dashboard

## Estrutura das Rotas

- `GET /auth/google` → Inicia o processo de login
- `GET /auth/google/callback` → Processa o retorno do Google
- `GET /auth/google/callback-page` → Página de callback (frontend)

## Controladores

- `SocialLoginController` → Gerencia a autenticação social
- `GoogleController` → Controlador alternativo (não usado atualmente)

## Componentes Vue

- `GoogleLoginButton.vue` → Botão de login com Google
- Integrado na página de login (`Login.vue`) 