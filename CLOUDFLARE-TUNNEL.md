# 🌐 Cloudflare Tunnel - Solução Simples para Múltiplos Dispositivos

Esta é a **solução mais simples** para testar o sistema em múltiplos dispositivos, sem problemas de CORS, IP de rede ou firewall.

## 🎯 Como Funciona

1. **Vite com Proxy:** O Vite serve o frontend e faz proxy das rotas do Laravel
2. **Cloudflare Tunnel:** Cria uma URL HTTPS pública que acessa seu localhost
3. **Mesmo Domínio:** Frontend e backend ficam no mesmo domínio (sem CORS)

## 🚀 Início Rápido

### 1. Executar Script Automático
```bash
./start-tunnel.sh
```

Este script:
- ✅ Inicia o Laravel em localhost:8000
- ✅ Inicia o Vite em localhost:5173  
- ✅ Cria o Cloudflare Tunnel
- ✅ Para todos os serviços quando você parar

### 2. Comandos Manuais (se preferir)
```bash
# Terminal 1 - Laravel
php artisan serve --host 127.0.0.1 --port 8000

# Terminal 2 - Vite
npm run dev

# Terminal 3 - Cloudflare Tunnel
cloudflared tunnel --url http://localhost:5173
```

## 📱 Como Usar no Smartphone

1. **Execute o script:** `./start-tunnel.sh`
2. **Aguarde** o túnel ser criado
3. **Copie a URL HTTPS** que aparecer (ex: `https://abc123.trycloudflare.com`)
4. **Abra no smartphone** - funciona de qualquer lugar!

## ⚙️ Configurações Aplicadas

### Vite (vite.config.js)
```javascript
server: {
    host: 'localhost',
    port: 5173,
    proxy: {
        '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/sanctum': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/broadcasting': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/storage': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/pt': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/en': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
        '/es': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false }
    }
}
```

### Laravel (.env)
```env
APP_URL=http://localhost:8000
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=false
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

## 🔧 Vantagens desta Solução

✅ **Sem CORS:** Frontend e backend no mesmo domínio  
✅ **Sem IP de rede:** Funciona de qualquer lugar  
✅ **Sem firewall:** Não precisa liberar portas  
✅ **HTTPS automático:** Cloudflare fornece certificado  
✅ **Simples:** Um comando resolve tudo  

## 🚨 Solução de Problemas

### "cloudflared não encontrado"
```bash
# Baixar e instalar
wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64
chmod +x cloudflared-linux-amd64
sudo mv cloudflared-linux-amd64 /usr/local/bin/cloudflared
```

### "Porta em uso"
```bash
# Parar processos
pkill -f "npm run dev"
pkill -f "php artisan serve"

# Ou reiniciar o sistema
```

### "Túnel não conecta"
- Verifique se o Laravel e Vite estão rodando
- Confirme que as portas 8000 e 5173 estão livres
- Tente novamente com `./start-tunnel.sh`

## 📋 Fluxo de Trabalho

1. **Desenvolvimento local:**
   - Edite arquivos normalmente
   - HMR funciona no localhost

2. **Teste em dispositivos:**
   - Execute `./start-tunnel.sh`
   - Use a URL HTTPS no smartphone
   - Teste funcionalidades

3. **Voltar ao desenvolvimento:**
   - Pressione `Ctrl+C` no túnel
   - Todos os serviços param automaticamente
   - Continue desenvolvendo

## 🎯 URLs de Acesso

- **Desenvolvimento:** http://localhost:5173
- **Smartphone:** https://[random].trycloudflare.com (fornecido pelo túnel)
- **Backend:** http://127.0.0.1:8000 (via proxy do Vite)

## 🔄 Restaurar Configuração Anterior

Se quiser voltar ao método de IP de rede:

```bash
# Restaurar .env
cp .env.backup.network .env

# Restaurar vite.config.js
git checkout vite.config.js

# Limpar cache
php artisan config:clear
php artisan cache:clear
```

## 💡 Dicas

- **Use o script:** `./start-tunnel.sh` é mais confiável
- **URLs temporárias:** O túnel gera URLs diferentes a cada execução
- **Qualquer rede:** Funciona em Wi-Fi, 4G, etc.
- **Múltiplos dispositivos:** A mesma URL funciona em todos

## 🎉 Resultado

Com esta solução, você pode:
- ✅ Testar em qualquer smartphone
- ✅ Não se preocupar com CORS
- ✅ Não configurar firewall
- ✅ Não depender de IP de rede
- ✅ Ter HTTPS automático
- ✅ Desenvolver normalmente

**É a solução mais simples e eficaz!** 🚀
