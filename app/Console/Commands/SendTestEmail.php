<?php

namespace App\Console\Commands;

use App\Mail\TaskCreatedMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia um email de teste com a nova interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Criar um usuário fictício para o teste
        $user = new User([
            'name' => 'Usuário Teste',
            'email' => $email
        ]);
        
        // Criar uma tarefa fictícia para o teste
        $task = new Task([
            'id' => 999,
            'title' => 'Tarefa de Teste - Nova Interface',
            'description' => 'Esta é uma tarefa de teste para demonstrar a nova interface de email com a logo do TaskForce!',
            'priority' => 'high',
            'status' => 'pending',
            'category' => 'Teste',
            'due_date' => now()->addDays(7),
            'estimated_hours' => 2,
            'tags' => ['teste', 'interface', 'email'],
            'created_at' => now()
        ]);
        
        // Criar um criador fictício
        $creator = new User([
            'name' => 'Sistema TaskForce',
            'email' => 'sistema@taskforce.com'
        ]);
        
        try {
            // Enviar o email de teste
            Mail::to($email)->send(new TaskCreatedMail($task, $creator, $user));
            
            $this->info("✅ Email de teste enviado com sucesso para: {$email}");
            $this->info("📧 Verifique sua caixa de entrada para ver a nova interface!");
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao enviar email: " . $e->getMessage());
        }
    }
}
