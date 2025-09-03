#!/bin/bash

echo "🚀 Iniciando Sistema de Tarefas para Teste em Rede"
echo "=================================================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Mostrar IP atual
echo "📍 IP Local: $(hostname -I | awk '{print $1}')"
echo ""

# Verificar se as portas estão abertas no firewall
echo "🔒 Verificando firewall..."
if sudo firewall-cmd --query-port=8000/tcp; then
    echo "✅ Porta 8000 (Laravel) está liberada"
else
    echo "❌ Porta 8000 não está liberada"
fi

if sudo firewall-cmd --query-port=5173/tcp; then
    echo "✅ Porta 5173 (Vite) está liberada"
else
    echo "❌ Porta 5173 não está liberada"
fi

echo ""

# Verificar configurações
echo "⚙️  Verificando configurações..."
if grep -q "APP_URL=http://192.168.0.28:8000" .env; then
    echo "✅ APP_URL configurado corretamente"
else
    echo "❌ APP_URL não está configurado corretamente"
fi

if grep -q "SANCTUM_STATEFUL_DOMAINS=192.168.0.28:5173" .env; then
    echo "✅ SANCTUM configurado corretamente"
else
    echo "❌ SANCTUM não está configurado corretamente"
fi

echo ""

echo "📱 URLs para teste em dispositivos móveis:"
echo "   Frontend: http://192.168.0.28:5173"
echo "   Backend:  http://192.168.0.28:8000"
echo ""

echo "🔄 Para iniciar os serviços:"
echo "   1. Terminal 1: php artisan serve --host 0.0.0.0 --port 8000"
echo "   2. Terminal 2: npm run dev -- --host"
echo ""

echo "⚠️  IMPORTANTE:"
echo "   - Certifique-se de que todos os dispositivos estão na mesma rede Wi-Fi"
echo "   - Desative 'AP Isolation' no roteador se necessário"
echo "   - Use exatamente os IPs mostrados acima"
echo ""

echo "🎯 Configuração concluída! Agora você pode iniciar os serviços."
