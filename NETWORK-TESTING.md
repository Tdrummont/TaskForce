# 🌐 Teste em Múltiplos Dispositivos

Este guia explica como configurar e testar o Sistema de Tarefas em múltiplos dispositivos na mesma rede Wi-Fi.

## 📋 Pré-requisitos

- Todos os dispositivos devem estar na mesma rede Wi-Fi
- Desative "AP Isolation" no roteador se necessário
- Fedora/Linux com firewall configurado

## 🚀 Configuração Rápida

### 1. Executar Script de Configuração
```bash
./start-network-test.sh
```

### 2. Iniciar Serviços

**Terminal 1 - Backend Laravel:**
```bash
php artisan serve --host 0.0.0.0 --port 8000
```

**Terminal 2 - Frontend Vite:**
```bash
npm run dev -- --host
```

## 📱 URLs para Teste

- **Frontend (Vue.js):** http://192.168.0.28:5173
- **Backend (Laravel):** http://192.168.0.28:8000

## ⚙️ Configurações Aplicadas

### Vite (vite.config.js)
- `host: true` - Aceita conexões externas
- `hmr.host: '192.168.0.28'` - HMR configurado para IP local

### Laravel (.env)
- `APP_URL=http://192.168.0.28:8000`
- `SESSION_DOMAIN=` (vazio para IP local)
- `SESSION_SECURE_COOKIE=false`
- `SANCTUM_STATEFUL_DOMAINS=192.168.0.28:5173`

### CORS (config/cors.php)
- `allowed_origins: ['http://192.168.0.28:5173']`
- `supports_credentials: true`

### Firewall
- Porta 8000/tcp liberada
- Porta 5173/tcp liberada

## 🔧 Solução de Problemas

### CORS Errors
- Verifique se o IP no `allowed_origins` está correto
- Execute `php artisan config:clear`

### Dispositivo não consegue acessar
- Confirme que estão na mesma rede Wi-Fi
- Verifique se "AP Isolation" está desativado no roteador
- Teste com `ping 192.168.0.28` do dispositivo móvel

### HMR não funciona
- Verifique se o IP no `vite.config.js` está correto
- Confirme que `npm run dev -- --host` está rodando

## 📱 Testando em Dispositivos Móveis

1. **Android/iPhone:** Abra o navegador
2. **Digite:** http://192.168.0.28:5173
3. **Teste:** Login, navegação, funcionalidades

## 🔄 Restaurar Configuração Local

Para voltar ao desenvolvimento local:

```bash
# Restaurar .env original
cp .env.backup.network .env

# Limpar cache
php artisan config:clear
php artisan cache:clear

# Restaurar vite.config.js
git checkout vite.config.js
```

## 📞 Suporte

Se encontrar problemas:
1. Execute `./start-network-test.sh` para verificar configurações
2. Verifique logs do Laravel em `storage/logs/`
3. Confirme conectividade de rede entre dispositivos
