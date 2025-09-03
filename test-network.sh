#!/bin/bash

echo "🧪 Teste de Conectividade de Rede"
echo "=================================="
echo ""

# Verificar IP atual
CURRENT_IP=$(hostname -I | awk '{print $1}')
echo "📍 IP Local: $CURRENT_IP"
echo ""

# Testar conectividade do Laravel
echo "🔍 Testando Laravel (Backend)..."
if curl -s -o /dev/null -w "%{http_code}" "http://$CURRENT_IP:8000" | grep -q "302"; then
    echo "✅ Laravel respondendo em http://$CURRENT_IP:8000"
else
    echo "❌ Laravel não está respondendo"
fi

# Testar conectividade do Vite
echo "🔍 Testando Vite (Frontend)..."
if curl -s -o /dev/null -w "%{http_code}" "http://$CURRENT_IP:5173/resources/js/app.js" | grep -q "200"; then
    echo "✅ Vite respondendo em http://$CURRENT_IP:5173"
else
    echo "❌ Vite não está respondendo"
fi

echo ""

# Verificar portas abertas
echo "🔒 Verificando portas no firewall..."
if sudo firewall-cmd --query-port=8000/tcp; then
    echo "✅ Porta 8000 (Laravel) liberada"
else
    echo "❌ Porta 8000 não liberada"
fi

if sudo firewall-cmd --query-port=5173/tcp; then
    echo "✅ Porta 5173 (Vite) liberada"
else
    echo "❌ Porta 5173 não liberada"
fi

echo ""

# Verificar processos rodando
echo "🔄 Verificando processos..."
if netstat -tlnp | grep "$CURRENT_IP:8000" > /dev/null; then
    echo "✅ Laravel rodando em $CURRENT_IP:8000"
else
    echo "❌ Laravel não está rodando"
fi

if netstat -tlnp | grep "$CURRENT_IP:5173" > /dev/null; then
    echo "✅ Vite rodando em $CURRENT_IP:5173"
else
    echo "❌ Vite não está rodando"
fi

echo ""

# Testar conectividade externa
echo "🌐 Testando conectividade externa..."
if ping -c 1 -W 2 8.8.8.8 > /dev/null 2>&1; then
    echo "✅ Conectividade com internet OK"
else
    echo "❌ Sem conectividade com internet"
fi

echo ""

# URLs para teste
echo "📱 URLs para teste em dispositivos móveis:"
echo "   Frontend: http://$CURRENT_IP:5173"
echo "   Backend:  http://$CURRENT_IP:8000"
echo ""

# Instruções de teste
echo "🎯 Para testar no smartphone:"
echo "   1. Certifique-se de estar na mesma rede Wi-Fi"
echo "   2. Abra o navegador"
echo "   3. Digite: http://$CURRENT_IP:5173"
echo "   4. Teste o login e navegação"
echo ""

# Verificar configurações
echo "⚙️  Verificando configurações..."
if grep -q "APP_URL=http://$CURRENT_IP:8000" .env; then
    echo "✅ APP_URL configurado corretamente"
else
    echo "❌ APP_URL não configurado corretamente"
fi

if grep -q "SANCTUM_STATEFUL_DOMAINS=$CURRENT_IP:5173" .env; then
    echo "✅ SANCTUM configurado corretamente"
else
    echo "❌ SANCTUM não configurado corretamente"
fi

echo ""
echo "�� Teste concluído!"
