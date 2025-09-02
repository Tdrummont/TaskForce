<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-deadlines {--days=3 : Days ahead to check for upcoming deadlines} {--notify : Send notifications to users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica prazos de tarefas e gera notificações';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysAhead = (int) $this->option('days');
        $shouldNotify = $this->option('notify');

        $this->info('🔍 Verificando prazos de tarefas...');

        $now = Carbon::now();
        $deadline = $now->copy()->addDays($daysAhead);

        // Buscar tarefas vencidas
        $overdueTasks = Task::where('due_date', '<', $now)
            ->where('status', '!=', 'completed')
            ->with(['creator', 'assignee'])
            ->get();

        // Buscar tarefas que vencem hoje
        $dueTodayTasks = Task::whereDate('due_date', $now->toDateString())
            ->where('status', '!=', 'completed')
            ->with(['creator', 'assignee'])
            ->get();

        // Buscar tarefas que vencem em breve
        $dueSoonTasks = Task::whereBetween('due_date', [
                $now->copy()->addDay()->toDateString(),
                $deadline->toDateString()
            ])
            ->where('status', '!=', 'completed')
            ->with(['creator', 'assignee'])
            ->get();

        $this->displayResults($overdueTasks, $dueTodayTasks, $dueSoonTasks);

        if ($shouldNotify) {
            $this->sendNotifications($overdueTasks, $dueTodayTasks, $dueSoonTasks);
        }

        // Log das verificações
        $this->logResults($overdueTasks, $dueTodayTasks, $dueSoonTasks);

        return 0;
    }

    /**
     * Exibe os resultados no console
     */
    private function displayResults($overdueTasks, $dueTodayTasks, $dueSoonTasks): void
    {
        $this->newLine();
        $this->info('📊 Resumo dos prazos:');
        
        if ($overdueTasks->isNotEmpty()) {
            $this->error("   ⚠️  {$overdueTasks->count()} tarefa(s) vencida(s):");
            foreach ($overdueTasks as $task) {
                $this->line("      • '{$task->title}' - Venceu em " . $task->due_date->format('d/m/Y'));
                $this->line("        Responsável: " . ($task->assignee ? $task->assignee->name : $task->creator->name));
            }
        }

        if ($dueTodayTasks->isNotEmpty()) {
            $this->warn("   ⏰ {$dueTodayTasks->count()} tarefa(s) vence(m) hoje:");
            foreach ($dueTodayTasks as $task) {
                $this->line("      • '{$task->title}' - Prioridade: " . $this->getPriorityLabel($task->priority));
                $this->line("        Responsável: " . ($task->assignee ? $task->assignee->name : $task->creator->name));
            }
        }

        if ($dueSoonTasks->isNotEmpty()) {
            $this->info("   📅 {$dueSoonTasks->count()} tarefa(s) vence(m) em breve:");
            foreach ($dueSoonTasks as $task) {
                $this->line("      • '{$task->title}' - Vence em " . $task->due_date->format('d/m/Y'));
                $this->line("        Responsável: " . ($task->assignee ? $task->assignee->name : $task->creator->name));
            }
        }

        if ($overdueTasks->isEmpty() && $dueTodayTasks->isEmpty() && $dueSoonTasks->isEmpty()) {
            $this->info("   ✅ Nenhuma tarefa com prazo próximo encontrada.");
        }
    }

    /**
     * Envia notificações aos usuários
     */
    private function sendNotifications($overdueTasks, $dueTodayTasks, $dueSoonTasks): void
    {
        $this->newLine();
        $this->info('📧 Enviando notificações...');

        $notificationsSent = 0;

        // Agrupar tarefas por usuário
        $userTasks = [];

        foreach ($overdueTasks as $task) {
            $userId = $task->assignee ? $task->assignee->id : $task->creator->id;
            if (!isset($userTasks[$userId])) {
                $userTasks[$userId] = ['overdue' => [], 'due_today' => [], 'due_soon' => []];
            }
            $userTasks[$userId]['overdue'][] = $task;
        }

        foreach ($dueTodayTasks as $task) {
            $userId = $task->assignee ? $task->assignee->id : $task->creator->id;
            if (!isset($userTasks[$userId])) {
                $userTasks[$userId] = ['overdue' => [], 'due_today' => [], 'due_soon' => []];
            }
            $userTasks[$userId]['due_today'][] = $task;
        }

        foreach ($dueSoonTasks as $task) {
            $userId = $task->assignee ? $task->assignee->id : $task->creator->id;
            if (!isset($userTasks[$userId])) {
                $userTasks[$userId] = ['overdue' => [], 'due_today' => [], 'due_soon' => []];
            }
            $userTasks[$userId]['due_soon'][] = $task;
        }

        foreach ($userTasks as $userId => $tasks) {
            $user = User::find($userId);
            if (!$user) continue;

            try {
                $this->sendUserNotification($user, $tasks);
                $notificationsSent++;
                $this->line("   ✅ Notificação enviada para {$user->name}");
            } catch (\Exception $e) {
                $this->error("   ❌ Erro ao enviar notificação para {$user->name}: {$e->getMessage()}");
                Log::error("Erro ao enviar notificação de prazo", [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("   📨 {$notificationsSent} notificação(ões) enviada(s)");
    }

    /**
     * Envia notificação para um usuário específico
     */
    private function sendUserNotification(User $user, array $tasks): void
    {
        // Simular envio de notificação
        // Em um ambiente real, aqui você enviaria email, push notification, etc.
        
        $message = "Olá {$user->name},\n\n";
        
        if (!empty($tasks['overdue'])) {
            $message .= "⚠️  Você tem " . count($tasks['overdue']) . " tarefa(s) vencida(s):\n";
            foreach ($tasks['overdue'] as $task) {
                $message .= "   • {$task->title} (Venceu em " . $task->due_date->format('d/m/Y') . ")\n";
            }
            $message .= "\n";
        }

        if (!empty($tasks['due_today'])) {
            $message .= "⏰ Você tem " . count($tasks['due_today']) . " tarefa(s) que vence(m) hoje:\n";
            foreach ($tasks['due_today'] as $task) {
                $message .= "   • {$task->title} (Prioridade: " . $this->getPriorityLabel($task->priority) . ")\n";
            }
            $message .= "\n";
        }

        if (!empty($tasks['due_soon'])) {
            $message .= "📅 Você tem " . count($tasks['due_soon']) . " tarefa(s) que vence(m) em breve:\n";
            foreach ($tasks['due_soon'] as $task) {
                $message .= "   • {$task->title} (Vence em " . $task->due_date->format('d/m/Y') . ")\n";
            }
            $message .= "\n";
        }

        $message .= "Acesse o sistema para gerenciar suas tarefas.\n\n";
        $message .= "Atenciosamente,\nSistema de Gerenciamento de Tarefas";

        // Salvar notificação no log (simulação)
        Log::info("Notificação de prazo enviada", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'message' => $message
        ]);

        // Simular delay de envio
        usleep(rand(100000, 300000)); // 0.1 a 0.3 segundos
    }

    /**
     * Registra os resultados no log
     */
    private function logResults($overdueTasks, $dueTodayTasks, $dueSoonTasks): void
    {
        Log::info("Verificação de prazos concluída", [
            'overdue_count' => $overdueTasks->count(),
            'due_today_count' => $dueTodayTasks->count(),
            'due_soon_count' => $dueSoonTasks->count(),
            'total_checked' => $overdueTasks->count() + $dueTodayTasks->count() + $dueSoonTasks->count()
        ]);
    }

    /**
     * Retorna o label da prioridade
     */
    private function getPriorityLabel(string $priority): string
    {
        return match($priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => $priority
        };
    }
} 