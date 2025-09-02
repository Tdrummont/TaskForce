# 📧 Configuração do Gmail SMTP para Envio de Emails

## 🔧 Problema Identificado

O sistema está configurado com `MAIL_MAILER=log`, que apenas registra os emails no log em vez de enviá-los realmente.

## ✅ Solução: Configurar Gmail SMTP

### 1. Preparar Conta Google

1. **Acesse**: https://myaccount.google.com/security
2. **Ative a autenticação de 2 fatores** (se ainda não estiver ativa)
3. **Vá para "Senhas de app"**
4. **Gere uma senha para "Email"**
5. **Copie a senha gerada** (16 caracteres)

### 2. Configurar Variáveis no .env

Edite seu arquivo `.env` e altere estas linhas:

```env
# ANTES (não funciona)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# DEPOIS (funciona com Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=SEU-EMAIL@gmail.com
MAIL_PASSWORD=SUA-SENHA-DE-APP
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=SEU-EMAIL@gmail.com
MAIL_FROM_NAME="Iron Force Tasks"
```

### 3. Exemplo Completo

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seuemail@gmail.com
MAIL_FROM_NAME="Iron Force Tasks"

# Broadcasting (já configurado)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=2045091
PUSHER_APP_KEY=661cf3c78faa86d8e332
PUSHER_APP_SECRET=fc92c0e1926a76661056
PUSHER_APP_CLUSTER=sa1
```

## 🧪 Como Testar

### 1. Após configurar o .env

```bash
# Limpar cache de configuração
php artisan config:clear

# Verificar configuração
php artisan config:show mail
```

### 2. Testar envio de email

```bash
# Testar com comando Artisan
php artisan test:email-notifications
```

### 3. Testar em tempo real

1. **Reinicie o servidor**: `php artisan serve`
2. **Faça login** no sistema
3. **Crie uma tarefa** atribuída a outro usuário
4. **Verifique** se o email foi enviado

## 🔍 Troubleshooting

### Erro: "Username and Password not accepted"

**Causa**: Senha de app incorreta ou não configurada

**Solução**:
1. Verifique se a autenticação de 2 fatores está ativa
2. Gere uma nova senha de app
3. Use a senha de app, não a senha da conta

### Erro: "Connection refused"

**Causa**: Porta ou host incorretos

**Solução**:
- Use `smtp.gmail.com` como host
- Use porta `587` para TLS
- Use porta `465` para SSL

### Email não chega

**Verificações**:
1. **Spam**: Verifique a pasta de spam
2. **Configuração**: Confirme as variáveis no .env
3. **Logs**: Verifique `storage/logs/laravel.log`
4. **Firewall**: Verifique se não há bloqueio de porta 587

## 📱 Alternativas ao Gmail

### 1. Mailgun (Gratuito até 5.000 emails/mês)

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=seu-dominio.com
MAILGUN_SECRET=sua-chave-secreta
```

### 2. SendGrid (Gratuito até 100 emails/dia)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=sua-api-key
```

### 3. Amazon SES (Muito barato)

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=sua-access-key
AWS_SECRET_ACCESS_KEY=sua-secret-key
AWS_DEFAULT_REGION=us-east-1
```

## 🚀 Próximos Passos

1. **Configure o Gmail SMTP** seguindo o guia acima
2. **Teste o sistema** criando uma nova tarefa
3. **Verifique** se os emails chegam
4. **Configure notificações de login** também

## 📞 Suporte

Se ainda tiver problemas:
1. Verifique os logs: `tail -f storage/logs/laravel.log`
2. Execute: `php artisan config:show mail`
3. Teste: `php artisan test:email-notifications`
4. Verifique se a senha de app está correta

Após configurar o Gmail SMTP, o sistema enviará emails reais em vez de apenas registrá-los no log! 