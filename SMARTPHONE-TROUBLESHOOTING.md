# 📱 Solução de Problemas para Smartphones

Este guia resolve problemas específicos de conectividade em dispositivos móveis.

## ✅ Status Atual

- **Laravel:** ✅ Rodando em http://192.168.0.28:8000
- **Vite:** ✅ Rodando em http://192.168.0.28:5173
- **Firewall:** ✅ Portas 8000 e 5173 liberadas
- **Configurações:** ✅ Todas configuradas corretamente

## 🚨 Problemas Comuns e Soluções

### 1. **"Não consegue conectar" ou "Site não pode ser alcançado"**

**Causas possíveis:**
- Dispositivo não está na mesma rede Wi-Fi
- AP Isolation ativado no roteador
- Firewall do smartphone bloqueando

**Soluções:**
```bash
# No smartphone, teste a conectividade:
ping 192.168.0.28

# Se não funcionar, verifique:
# 1. Rede Wi-Fi (deve ser a mesma do PC)
# 2. Desative "AP Isolation" no roteador
# 3. Tente desativar temporariamente o firewall do smartphone
```

### 2. **"Página não encontrada" ou Erro 404**

**Causas possíveis:**
- URL incorreta
- Vite não está rodando
- Problema de configuração

**Soluções:**
```bash
# Verifique se o Vite está rodando:
./test-network.sh

# Se não estiver, inicie novamente:
./start-vite-ipv4.sh
```

### 3. **"Erro de CORS" ou "Bloqueado pelo navegador"**

**Causas possíveis:**
- Configuração CORS incorreta
- Cache do navegador

**Soluções:**
```bash
# Limpe o cache do Laravel:
php artisan config:clear
php artisan cache:clear

# No smartphone, limpe o cache do navegador
# Ou use modo incógnito/privado
```

### 4. **"Login não funciona" ou "Sessão expirada"**

**Causas possíveis:**
- Configuração de sessão incorreta
- Problema de cookies

**Soluções:**
```bash
# Verifique configurações no .env:
grep -E "(SESSION|SANCTUM)" .env

# Deve mostrar:
# SESSION_DOMAIN=
# SESSION_SECURE_COOKIE=false
# SANCTUM_STATEFUL_DOMAINS=192.168.0.28:5173
```

## 🔧 Testes de Diagnóstico

### Teste 1: Conectividade Básica
```bash
# No smartphone, abra o terminal ou use app de rede
ping 192.168.0.28
```

### Teste 2: Navegador
```bash
# Abra o navegador e digite:
http://192.168.0.28:5173

# Deve mostrar a página de login do Sistema de Tarefas
```

### Teste 3: Console do Navegador
```bash
# No smartphone, abra as ferramentas de desenvolvedor
# Verifique se há erros de JavaScript ou rede
```

## 📱 Configurações Específicas por Dispositivo

### Android
- **Chrome:** Deve funcionar normalmente
- **Firefox:** Pode ter problemas de CORS
- **Samsung Internet:** Pode ter problemas de cache

### iPhone/iPad
- **Safari:** Funciona bem
- **Chrome iOS:** Pode ter limitações
- **Firefox iOS:** Pode ter problemas de CORS

## 🌐 Configurações do Roteador

### Desativar AP Isolation
1. Acesse o painel do roteador (geralmente 192.168.1.1)
2. Procure por "AP Isolation" ou "Client Isolation"
3. **Desative** esta opção
4. Salve e reinicie o roteador

### Verificar Firewall
1. No painel do roteador, procure por "Firewall"
2. Verifique se as portas 8000 e 5173 estão liberadas
3. Se não estiverem, adicione-as

## 🚀 Comandos de Inicialização

### Iniciar Serviços
```bash
# Terminal 1 - Laravel
php artisan serve --host 0.0.0.0 --port 8000

# Terminal 2 - Vite
./start-vite-ipv4.sh
```

### Verificar Status
```bash
# Teste completo
./test-network.sh

# Verificar processos
netstat -tlnp | grep -E "(8000|5173)"
```

## 📞 Suporte

Se ainda não funcionar:

1. **Execute o teste completo:**
   ```bash
   ./test-network.sh
   ```

2. **Verifique os logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Teste em outro dispositivo:**
   - Tente em outro smartphone
   - Tente em um tablet
   - Tente em outro computador na rede

4. **Verifique a rede:**
   - Todos os dispositivos na mesma rede Wi-Fi?
   - AP Isolation desativado?
   - Firewall do roteador configurado?

## 🎯 URLs Finais

- **Frontend:** http://192.168.0.28:5173
- **Backend:** http://192.168.0.28:8000

**Lembre-se:** Use exatamente estes IPs, não localhost ou 127.0.0.1!
