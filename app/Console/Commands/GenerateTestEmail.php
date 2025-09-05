<?php

namespace App\Console\Commands;

use App\Mail\TaskCreatedMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class GenerateTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-html';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um arquivo HTML do email de teste para visualização';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Criar um usuário fictício para o teste
        $user = new User([
            'name' => 'Usuário Teste',
            'email' => 'teste@taskforce.com'
        ]);
        
        // Criar uma tarefa fictícia para o teste
        $task = new Task();
        $task->id = 999;
        $task->title = 'Tarefa de Teste - Nova Interface';
        $task->description = 'Esta é uma tarefa de teste para demonstrar a nova interface de email com a logo do TaskForce!';
        $task->priority = 'high';
        $task->status = 'pending';
        $task->category = 'Teste';
        $task->due_date = now()->addDays(7);
        $task->estimated_hours = 2;
        $task->tags = ['teste', 'interface', 'email'];
        $task->created_at = now();
        
        // Criar um criador fictício
        $creator = new User([
            'name' => 'Sistema TaskForce',
            'email' => 'sistema@taskforce.com'
        ]);
        
        try {
            // Renderizar o template do email
            $html = View::make('emails.tasks.created', [
                'task' => $task,
                'creator' => $creator,
                'user' => $user
            ])->render();
            
            // Salvar o HTML em um arquivo
            $filename = storage_path('app/email_test.html');
            file_put_contents($filename, $html);
            
            $this->info("✅ Email HTML gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$filename}");
            $this->info("🌐 Abra o arquivo no seu navegador para ver o resultado!");
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar email: " . $e->getMessage());
        }
    }
}
