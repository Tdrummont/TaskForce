<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTANDO ROTA SIMPLES\n";
echo "========================\n\n";

try {
    // Verificar se a rota existe
    echo "1. Verificando se a rota /api/notifications existe...\n";
    
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $notificationRoute = null;
    
    foreach ($routes as $route) {
        if ($route->uri() === 'api/notifications' && in_array('GET', $route->methods())) {
            $notificationRoute = $route;
            break;
        }
    }
    
    if ($notificationRoute) {
        echo "   ✅ Rota encontrada!\n";
        echo "   - URI: " . $notificationRoute->uri() . "\n";
        echo "   - Métodos: " . implode(', ', $notificationRoute->methods()) . "\n";
        echo "   - Controller: " . $notificationRoute->getActionName() . "\n";
        echo "   - Middleware: " . implode(', ', $notificationRoute->middleware()) . "\n";
    } else {
        echo "   ❌ Rota não encontrada!\n";
    }
    
    echo "\n2. Verificando middleware da rota...\n";
    if ($notificationRoute) {
        $middleware = $notificationRoute->middleware();
        foreach ($middleware as $mw) {
            echo "   - {$mw}\n";
        }
    }
    
    echo "\n3. Verificando se o usuário está autenticado...\n";
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        echo "   ✅ Usuário autenticado: {$user->name} (ID: {$user->id})\n";
    } else {
        echo "   ❌ Nenhum usuário autenticado\n";
    }
    
    echo "\n4. Testando acesso direto ao controller...\n";
    try {
        $controller = new \App\Http\Controllers\NotificationController();
        $request = new \Illuminate\Http\Request();
        $request->setUserResolver(function() {
            return \App\Models\User::first();
        });
        
        $response = $controller->apiIndex($request);
        echo "   ✅ Controller funcionando!\n";
        echo "   - Status: " . $response->getStatusCode() . "\n";
        echo "   - Conteúdo: " . substr($response->getContent(), 0, 100) . "...\n";
        
    } catch (Exception $e) {
        echo "   ❌ Erro no controller: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
} 