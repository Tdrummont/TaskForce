<?php

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

echo "🧪 Testando Mensagens Flash...\n\n";

// Simular uma sessão
Session::start();

echo "1. Definindo mensagem flash...\n";
Session::flash('email_sent', [
    'type' => 'success',
    'title' => 'Tarefa Delegada!',
    'message' => 'Tarefa delegada para Usuário Teste (teste@example.com)'
]);

echo "2. Verificando se a mensagem foi definida...\n";
$flash = Session::get('flash');
if ($flash && isset($flash['email_sent'])) {
    echo "   ✅ Mensagem flash definida com sucesso!\n";
    echo "   📝 Tipo: " . $flash['email_sent']['type'] . "\n";
    echo "   📝 Título: " . $flash['email_sent']['title'] . "\n";
    echo "   📝 Mensagem: " . $flash['email_sent']['message'] . "\n";
} else {
    echo "   ❌ Mensagem flash não foi definida\n";
}

echo "\n3. Verificando estrutura da sessão...\n";
$allSession = Session::all();
echo "   📊 Total de itens na sessão: " . count($allSession) . "\n";

if (isset($allSession['flash'])) {
    echo "   ✅ Chave 'flash' encontrada na sessão\n";
    echo "   📋 Conteúdo da chave 'flash':\n";
    print_r($allSession['flash']);
} else {
    echo "   ❌ Chave 'flash' não encontrada na sessão\n";
    echo "   📋 Chaves disponíveis na sessão:\n";
    foreach (array_keys($allSession) as $key) {
        echo "   - {$key}\n";
    }
}

echo "\n4. Verificando se a mensagem está acessível via flash()...\n";
$emailSent = Session::get('flash.email_sent');
if ($emailSent) {
    echo "   ✅ Mensagem acessível via flash.email_sent\n";
    print_r($emailSent);
} else {
    echo "   ❌ Mensagem não acessível via flash.email_sent\n";
}

echo "\n5. Testando limpeza da mensagem...\n";
Session::forget('flash.email_sent');
$emailSentAfter = Session::get('flash.email_sent');
if (!$emailSentAfter) {
    echo "   ✅ Mensagem foi limpa com sucesso\n";
} else {
    echo "   ❌ Mensagem não foi limpa\n";
}

echo "\n🎯 Diagnóstico:\n";
echo "========================\n";

if (isset($allSession['flash']) && isset($allSession['flash']['email_sent'])) {
    echo "✅ Mensagens flash estão funcionando corretamente\n";
    echo "✅ Estrutura da sessão está correta\n";
    echo "✅ Mensagens podem ser acessadas pelo frontend\n";
} else {
    echo "❌ Problema com mensagens flash\n";
    echo "🔍 Verificar:\n";
    echo "   - Configuração do middleware de sessão\n";
    echo "   - Configuração do Inertia\n";
    echo "   - Estrutura das mensagens flash\n";
}

echo "\n💡 Para testar no frontend:\n";
echo "1. Acesse uma página que defina mensagens flash\n";
echo "2. Abra o console do navegador (F12)\n";
echo "3. Verifique se page.props.flash contém as mensagens\n";
echo "4. Verifique se o componente EmailNotificationSnackbar está funcionando\n"; 