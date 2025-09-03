#!/bin/bash

echo "🚀 Iniciando Vite com IPv4 forçado..."
echo "======================================"

# Parar qualquer processo Vite existente
pkill -f "npm run dev" 2>/dev/null
sleep 2

# Verificar se as portas estão livres
echo "🔍 Verificando portas..."
if netstat -tlnp | grep ":5173" > /dev/null; then
    echo "❌ Porta 5173 ainda está em uso"
    exit 1
fi

echo "✅ Porta 5173 está livre"

# Configurar variáveis de ambiente para forçar IPv4
export NODE_OPTIONS="--dns-result-order=ipv4first"
export VITE_HOST="192.168.0.28"

echo "🌐 Configurando para IPv4..."
echo "   NODE_OPTIONS: $NODE_OPTIONS"
echo "   VITE_HOST: $VITE_HOST"

# Iniciar Vite com configurações específicas
echo "🚀 Iniciando Vite..."
npm run dev -- --host 192.168.0.28 &

# Aguardar inicialização
echo "⏳ Aguardando inicialização..."
sleep 8

# Verificar se está rodando
echo "🔍 Verificando status..."
if netstat -tlnp | grep "192.168.0.28:5173" > /dev/null; then
    echo "✅ Vite rodando em IPv4: 192.168.0.28:5173"
    echo ""
    echo "📱 URLs para teste:"
    echo "   Frontend: http://192.168.0.28:5173"
    echo "   Backend:  http://192.168.0.28:8000"
    echo ""
    echo "🎯 Teste no smartphone: http://192.168.0.28:5173"
else
    echo "❌ Vite não está rodando em IPv4"
    echo "🔍 Verificando todas as portas 5173:"
    netstat -tlnp | grep ":5173"
fi
