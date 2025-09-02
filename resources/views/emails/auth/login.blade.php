@component('mail::message')
# Login Detectado

Olá **{{ $user->name }}**,

Detectamos um novo login na sua conta.

## Detalhes do Login

**Usuário:** {{ $user->name }} ({{ $user->email }})

**Data e Hora:** {{ $loginTime->format('d/m/Y H:i:s') }}

@if($ipAddress)
**Endereço IP:** {{ $ipAddress }}
@endif

@if($userAgent)
**Navegador/Dispositivo:** {{ $userAgent }}
@endif

## Ações Recomendadas

1. **Verificar** se foi você que fez o login
2. **Alterar senha** se não reconhecer o acesso
3. **Verificar** dispositivos conectados
4. **Reportar** atividade suspeita se necessário

@component('mail::button', ['url' => $dashboardUrl, 'color' => 'primary'])
Acessar Dashboard
@endcomponent

## Segurança da Conta

- Mantenha sua senha segura e única
- Ative a autenticação de dois fatores se disponível
- Não compartilhe suas credenciais
- Faça logout ao usar dispositivos públicos

Se você não fez este login, entre em contato imediatamente com o suporte técnico.

Obrigado,<br>
{{ config('app.name') }}

---
*Esta notificação foi enviada automaticamente pelo sistema de segurança.*
@endcomponent 