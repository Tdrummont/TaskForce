# 📧 Configuração de Email para Notificações

## 🔧 Variáveis de Ambiente Necessárias

Adicione estas variáveis ao seu arquivo `.env`:

### 1. Configuração de Email

```env
# Driver de email (recomendado: smtp para produção)
MAIL_MAILER=smtp

# Configuração SMTP
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app
MAIL_ENCRYPTION=tls

# Email de origem
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="Iron Force Tasks"
```

### 2. Configuração do Gmail

Para usar o Gmail, você precisa:

1. **Ativar autenticação de 2 fatores** na sua conta Google
2. **Gerar uma senha de app**:
   - Vá para https://myaccount.google.com/security
   - Clique em "Senhas de app"
   - Gere uma senha para "Email"
   - Use essa senha no `MAIL_PASSWORD`

### 3. Configuração do Pusher (para notificações em tempo real)

```env
# Broadcasting
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=seu_app_id
PUSHER_APP_KEY=sua_app_key
PUSHER_APP_SECRET=seu_app_secret
PUSHER_APP_CLUSTER=mt1

# Para o frontend
VITE_PUSHER_APP_KEY=sua_app_key
VITE_PUSHER_APP_CLUSTER=mt1
```

## 🚀 Como Testar

### 1. Execute o teste de configuração

```bash
php test_email_notifications.php
```

### 2. Teste em tempo real

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilação de assets
npm run dev

# Terminal 3: Queue para emails (opcional)
php artisan queue:work
```

### 3. Teste as funcionalidades

1. **Faça login** no sistema
2. **Verifique** se recebeu email de notificação
3. **Crie uma tarefa** atribuída a outro usuário
4. **Verifique** se o usuário recebeu email de notificação

## 📧 Provedores de Email Recomendados

### 1. Gmail (Gratuito)
- **Vantagens**: Fácil configuração, confiável
- **Desvantagens**: Limite de 500 emails/dia
- **Configuração**: Veja acima

### 2. Mailgun (Gratuito até 5.000 emails/mês)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=seu-dominio.com
MAILGUN_SECRET=sua-chave-secreta
```

### 3. SendGrid (Gratuito até 100 emails/dia)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=sua-api-key
```

### 4. Amazon SES (Muito barato)
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=sua-access-key
AWS_SECRET_ACCESS_KEY=sua-secret-key
AWS_DEFAULT_REGION=us-east-1
```

## 🔍 Troubleshooting

### Problemas Comuns

1. **Emails não são enviados**
   - Verifique se `MAIL_MAILER` está configurado
   - Confirme credenciais SMTP
   - Verifique logs em `storage/logs/laravel.log`

2. **Erro de autenticação Gmail**
   - Use senha de app, não senha da conta
   - Ative autenticação de 2 fatores
   - Verifique se "Acesso a app menos seguro" está desativado

3. **Emails vão para spam**
   - Configure SPF e DKIM no DNS
   - Use domínio próprio no `MAIL_FROM_ADDRESS`
   - Evite palavras que ativam filtros de spam

### Logs Úteis

```bash
# Ver logs de email
tail -f storage/logs/laravel.log | grep -i mail

# Ver configuração de email
php artisan config:show mail

# Testar envio de email
php artisan tinker
Mail::raw('Teste', function($message) { $message->to('teste@exemplo.com')->subject('Teste'); });
```

## 📱 Notificações em Tempo Real

O sistema também envia notificações via WebSocket usando Pusher:

1. **Configure o Pusher** (veja variáveis acima)
2. **Instale as dependências**:
   ```bash
   npm install laravel-echo pusher-js
   ```
3. **Compile os assets**:
   ```bash
   npm run dev
   ```

## 🎯 Funcionalidades Implementadas

- ✅ **Notificação de login** por email
- ✅ **Notificação de tarefa atribuída** por email
- ✅ **Notificações em tempo real** via WebSocket
- ✅ **Templates de email** personalizáveis
- ✅ **Logs detalhados** para debugging
- ✅ **Tratamento de erros** robusto

## 🚀 Próximos Passos

1. **Configure as variáveis** no `.env`
2. **Teste o sistema** com o script fornecido
3. **Personalize os templates** de email se necessário
4. **Configure filas** para melhor performance
5. **Monitore os logs** para identificar problemas 