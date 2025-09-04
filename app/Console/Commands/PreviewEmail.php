<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Mail\TaskDelegatedMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class PreviewEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preview:email {--user-email=} {--task-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera uma prévia do email de delegação';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->option('user-email') ?? 'gabyribeiro001@gmail.com';
        $taskId = $this->option('task-id');

        $this->info("👁️ Gerando prévia do email de delegação...");

        // Buscar usuário destinatário
        $delegatedTo = User::where('email', $userEmail)->first();
        if (!$delegatedTo) {
            $this->error("❌ Usuário com email {$userEmail} não encontrado!");
            return 1;
        }

        // Buscar tarefa
        if ($taskId) {
            $task = Task::find($taskId);
            if (!$task) {
                $this->error("❌ Tarefa com ID {$taskId} não encontrada!");
                return 1;
            }
        } else {
            $task = Task::latest()->first();
            if (!$task) {
                $this->error("❌ Nenhuma tarefa encontrada no sistema!");
                return 1;
            }
        }

        // Buscar usuário que está delegando
        $delegatedBy = User::find($task->created_by);
        if (!$delegatedBy) {
            $this->error("❌ Usuário criador da tarefa não encontrado!");
            return 1;
        }

        try {
            // Gerar HTML do email
            $html = View::make('emails.tasks.delegated', [
                'task' => $task,
                'delegatedBy' => $delegatedBy,
                'delegatedTo' => $delegatedTo
            ])->render();

            // Salvar em arquivo
            $filename = storage_path('app/email_preview.html');
            file_put_contents($filename, $html);

            $this->info("✅ Prévia do email gerada com sucesso!");
            $this->info("📁 Arquivo salvo em: {$filename}");
            $this->info("🌐 Abra o arquivo no navegador para visualizar");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar prévia: " . $e->getMessage());
            return 1;
        }
    }
}
