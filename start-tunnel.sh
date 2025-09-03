#!/bin/bash

echo "🚀 Iniciando Sistema com Cloudflare Tunnel"
echo "=========================================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Verificar se o cloudflared está instalado
if ! command -v cloudflared &> /dev/null; then
    echo "❌ Erro: cloudflared não está instalado"
    echo "💡 Execute: wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64"
    echo "💡 Depois: chmod +x cloudflared-linux-amd64 && sudo mv cloudflared-linux-amd64 /usr/local/bin/cloudflared"
    exit 1
fi

echo "✅ cloudflared encontrado"
echo ""

# Parar processos existentes
echo "🛑 Parando processos existentes..."
pkill -f "npm run dev" 2>/dev/null
pkill -f "php artisan serve" 2>/dev/null
sleep 2

# Verificar se as portas estão livres
echo "🔍 Verificando portas..."
if netstat -tlnp | grep ":8000" > /dev/null; then
    echo "❌ Porta 8000 ainda está em uso"
    exit 1
fi

if netstat -tlnp | grep ":5173" > /dev/null; then
    echo "❌ Porta 5173 ainda está em uso"
    exit 1
fi

echo "✅ Portas livres"
echo ""

# Iniciar Laravel (localhost)
echo "🚀 Iniciando Laravel..."
php artisan serve --host 127.0.0.1 --port 8000 &
LARAVEL_PID=$!
sleep 3

# Verificar se Laravel está rodando
if ! netstat -tlnp | grep "127.0.0.1:8000" > /dev/null; then
    echo "❌ Laravel não iniciou corretamente"
    exit 1
fi

echo "✅ Laravel rodando em http://127.0.0.1:8000"
echo ""

# Iniciar Vite (localhost)
echo "🚀 Iniciando Vite..."
npm run dev &
VITE_PID=$!
sleep 5

# Verificar se Vite está rodando
if ! netstat -tlnp | grep ":5173" > /dev/null; then
    echo "❌ Vite não iniciou corretamente"
    exit 1
fi

echo "✅ Vite rodando em http://localhost:5173"
echo ""

# Aguardar um pouco mais para o Vite inicializar completamente
sleep 3

# Iniciar Cloudflare Tunnel
echo "🌐 Iniciando Cloudflare Tunnel..."
echo "📱 O túnel será criado para http://localhost:5173"
echo ""

# Mostrar instruções
echo "🎯 INSTRUÇÕES:"
echo "   1. Aguarde o túnel ser criado"
echo "   2. Copie a URL HTTPS que aparecer"
echo "   3. Abra essa URL no smartphone"
echo "   4. Teste o login e funcionalidades"
echo ""

echo "🚀 Iniciando túnel..."
echo "   (Pressione Ctrl+C para parar todos os serviços)"
echo ""

# Iniciar o túnel
cloudflared tunnel --url http://localhost:5173

# Se chegou aqui, o usuário parou o túnel
echo ""
echo "🛑 Parando todos os serviços..."
kill $LARAVEL_PID 2>/dev/null
kill $VITE_PID 2>/dev/null
pkill -f "npm run dev" 2>/dev/null
pkill -f "php artisan serve" 2>/dev/null

echo "✅ Todos os serviços parados"
echo "🎯 Para iniciar novamente, execute: ./start-tunnel.sh"
