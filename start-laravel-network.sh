#!/bin/bash

echo "🚀 Iniciando Laravel na Rede"
echo "============================"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Verificar se o build foi feito
if [ ! -f "public/build/manifest.json" ]; then
    echo "❌ Erro: Build não encontrado. Execute 'npm run build' primeiro"
    exit 1
fi

echo "✅ Build encontrado"
echo ""

# Mostrar IP atual
CURRENT_IP=$(hostname -I | awk '{print $1}')
echo "📍 IP Local: $CURRENT_IP"
echo ""

# Verificar se as portas estão livres
echo "🔍 Verificando portas..."
if netstat -tlnp | grep ":8000" > /dev/null; then
    echo "❌ Porta 8000 ainda está em uso"
    echo "💡 Execute: pkill -f 'php artisan serve'"
    exit 1
fi

echo "✅ Porta 8000 está livre"
echo ""

# Verificar firewall
echo "🔒 Verificando firewall..."
if sudo firewall-cmd --query-port=8000/tcp; then
    echo "✅ Porta 8000 liberada no firewall"
else
    echo "⚠️  Porta 8000 não liberada no firewall"
    echo "💡 Execute: sudo firewall-cmd --add-port=8000/tcp --permanent && sudo firewall-cmd --reload"
fi

echo ""

# URLs para teste
echo "📱 URLs para teste em dispositivos móveis:"
echo "   Sistema: http://$CURRENT_IP:8000"
echo "   Login:  http://$CURRENT_IP:8000/pt/login"
echo ""

# Instruções
echo "🎯 Para testar no smartphone:"
echo "   1. Certifique-se de estar na mesma rede Wi-Fi"
echo "   2. Abra o navegador"
echo "   3. Digite: http://$CURRENT_IP:8000"
echo "   4. Teste o login e funcionalidades"
echo ""

echo "⚠️  IMPORTANTE:"
echo "   - Todos os dispositivos devem estar na mesma rede Wi-Fi"
echo "   - Desative 'AP Isolation' no roteador se necessário"
echo "   - Use exatamente o IP mostrado acima"
echo ""

echo "🚀 Iniciando Laravel..."
echo "   (Pressione Ctrl+C para parar)"
echo ""

# Iniciar Laravel na rede
php artisan serve --host 0.0.0.0 --port 8000
