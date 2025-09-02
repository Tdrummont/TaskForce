<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;

class TestFlashMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:flash-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se as mensagens flash estão funcionando corretamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando Mensagens Flash...');
        $this->newLine();

        // Simular uma sessão
        Session::start();

        $this->info('1. Definindo mensagem flash...');
        Session::flash('email_sent', [
            'type' => 'success',
            'title' => 'Tarefa Delegada!',
            'message' => 'Tarefa delegada para Usuário Teste (teste@example.com)'
        ]);

        $this->info('2. Verificando se a mensagem foi definida...');
        $flash = Session::get('flash');
        if ($flash && isset($flash['email_sent'])) {
            $this->info('   ✅ Mensagem flash definida com sucesso!');
            $this->info("   📝 Tipo: " . $flash['email_sent']['type']);
            $this->info("   📝 Título: " . $flash['email_sent']['title']);
            $this->info("   📝 Mensagem: " . $flash['email_sent']['message']);
        } else {
            $this->error('   ❌ Mensagem flash não foi definida');
        }

        $this->newLine();
        $this->info('3. Verificando estrutura da sessão...');
        $allSession = Session::all();
        $this->info("   📊 Total de itens na sessão: " . count($allSession));

        if (isset($allSession['flash'])) {
            $this->info('   ✅ Chave \'flash\' encontrada na sessão');
            $this->info('   📋 Conteúdo da chave \'flash\':');
            $this->line(json_encode($allSession['flash'], JSON_PRETTY_PRINT));
        } else {
            $this->error('   ❌ Chave \'flash\' não encontrada na sessão');
            $this->info('   📋 Chaves disponíveis na sessão:');
            foreach (array_keys($allSession) as $key) {
                $this->line("   - {$key}");
            }
        }

        $this->newLine();
        $this->info('4. Verificando se a mensagem está acessível via flash()...');
        $emailSent = Session::get('flash.email_sent');
        if ($emailSent) {
            $this->info('   ✅ Mensagem acessível via flash.email_sent');
            $this->line(json_encode($emailSent, JSON_PRETTY_PRINT));
        } else {
            $this->error('   ❌ Mensagem não acessível via flash.email_sent');
        }

        $this->newLine();
        $this->info('5. Testando limpeza da mensagem...');
        Session::forget('flash.email_sent');
        $emailSentAfter = Session::get('flash.email_sent');
        if (!$emailSentAfter) {
            $this->info('   ✅ Mensagem foi limpa com sucesso');
        } else {
            $this->error('   ❌ Mensagem não foi limpa');
        }

        $this->newLine();
        $this->info('🎯 Diagnóstico:');
        $this->line('========================');

        if (isset($allSession['flash']) && isset($allSession['flash']['email_sent'])) {
            $this->info('✅ Mensagens flash estão funcionando corretamente');
            $this->info('✅ Estrutura da sessão está correta');
            $this->info('✅ Mensagens podem ser acessadas pelo frontend');
        } else {
            $this->error('❌ Problema com mensagens flash');
            $this->info('🔍 Verificar:');
            $this->line('   - Configuração do middleware de sessão');
            $this->line('   - Configuração do Inertia');
            $this->line('   - Estrutura das mensagens flash');
        }

        $this->newLine();
        $this->info('💡 Para testar no frontend:');
        $this->line('1. Acesse uma página que defina mensagens flash');
        $this->line('2. Abra o console do navegador (F12)');
        $this->line('3. Verifique se page.props.flash contém as mensagens');
        $this->line('4. Verifique se o componente EmailNotificationSnackbar está funcionando');

        return 0;
    }
} 