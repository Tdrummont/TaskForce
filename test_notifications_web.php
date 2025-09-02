<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Simular aplicação Laravel
$app = new Application();

echo "🧪 Testando Rotas Web de Notificações...\n\n";

// Testar se as rotas estão registradas
echo "📋 Verificando rotas...\n";
$routes = shell_exec('php artisan route:list | grep notifications');
echo $routes;

echo "\n🔍 Verificando se as rotas estão acessíveis...\n";

// Testar rota de listagem
echo "📡 Testando GET /notifications...\n";
$response = shell_exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/notifications');
echo "Status: $response\n";

// Testar rota de marcar como lida
echo "📝 Testando PATCH /notifications/1/read...\n";
$response = shell_exec('curl -s -o /dev/null -w "%{http_code}" -X PATCH http://localhost:8000/notifications/1/read');
echo "Status: $response\n";

echo "\n✅ Teste concluído!\n";
echo "💡 Se os status forem 401, significa que as rotas estão funcionando mas precisam de autenticação\n";
echo "💡 Se os status forem 404, significa que há problema nas rotas\n";
echo "💡 Se os status forem 200, significa que está funcionando perfeitamente\n"; 