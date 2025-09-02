# 🔍 Checklist de Verificação do Google Cloud Console

## ✅ **APIs Necessárias (Habilitar no Google Cloud Console)**

1. **Google+ API** ou **Google Identity**
2. **Google+ Domains API**
3. **Google People API**

## ✅ **Configuração de Credenciais OAuth 2.0**

### **Tipo de Aplicativo:**
- ✅ Aplicativo da Web

### **URIs de Redirecionamento Autorizados:**
```
http://localhost:8000/auth/google/callback
```

### **URIs JavaScript Autorizados (se necessário):**
```
http://localhost:8000
```

## ✅ **Verificar Configurações**

### **1. Acesse Google Cloud Console:**
- https://console.cloud.google.com/
- Selecione seu projeto

### **2. Vá em "APIs e Serviços" > "Credenciais"**

### **3. Clique no seu ID do Cliente OAuth 2.0**

### **4. Verifique:**
- ✅ **ID do Cliente** está correto
- ✅ **Chave Secreta** está correta
- ✅ **URIs de redirecionamento** incluem exatamente: `http://localhost:8000/auth/google/callback`
- ✅ **Status** está "Ativadas"

## ✅ **Teste de Configuração**

### **1. Verificar se as APIs estão habilitadas:**
- Vá em "APIs e Serviços" > "Biblioteca"
- Procure e habilite:
  - Google+ API
  - Google Identity
  - Google People API

### **2. Verificar se o projeto está ativo:**
- Vá em "IAM e Admin" > "Configurações"
- Verifique se o projeto está ativo

### **3. Verificar se há restrições:**
- Vá em "APIs e Serviços" > "Tela de consentimento OAuth"
- Verifique se não há restrições que impeçam o login

## 🔧 **Solução de Problemas**

### **Erro "Missing required parameter: redirect_uri"**
- Verifique se o URI no Google Cloud Console é exatamente igual ao do .env
- Não deve ter barras extras ou diferenças

### **Erro "Invalid client"**
- Verifique se o Client ID e Client Secret estão corretos no .env
- Verifique se as credenciais estão ativas

### **Erro "Access blocked"**
- Verifique se as APIs necessárias estão habilitadas
- Verifique se o projeto está ativo

## 📝 **Configuração Final**

Após verificar tudo, seu .env deve ter:

```env
GOOGLE_CLIENT_ID=173401741666-l54s12rclh9e60up0feugvsl4dhacgme.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## 🧪 **Teste Final**

1. Limpe o cache: `php artisan config:clear`
2. Acesse: `http://localhost:8000/login`
3. Clique em "Entrar com Google"
4. Complete o processo de autorização 