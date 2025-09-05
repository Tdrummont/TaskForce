<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $assignedBy;
    public $assignedTo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $assignedBy, User $assignedTo)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
        $this->assignedTo = $assignedTo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Usar nosso template que funciona
        return (new MailMessage)
            ->subject("Task Force • Nova Tarefa Atribuída: {$this->task->title}")
            ->view('emails.taskforce.notification', [
                'subject' => "Task Force • Nova Tarefa Atribuída: {$this->task->title}",
                'title' => "Nova Tarefa Atribuída: {$this->task->title}",
                'recipientName' => $notifiable->name,
                'intro' => "Uma nova tarefa foi atribuída a você no sistema Task Force. Esta é uma excelente oportunidade para demonstrar suas habilidades e contribuir para o sucesso da equipe.",
                'highlights' => [
                    "✨ {$this->task->title}",
                    "📊 Status: " . ucfirst($this->task->status),
                    "🎯 Prioridade: " . ucfirst($this->task->priority)
                ],
                'infoItems' => [
                    'Atribuída por' => $this->assignedBy->name,
                    'Atribuída para' => $this->assignedTo->name,
                    'Referência' => "#TF-{$this->task->id}",
                    'Data de Criação' => $this->task->created_at->format('d/m/Y H:i'),
                    'Prioridade' => ucfirst($this->task->priority),
                    'Status' => ucfirst($this->task->status)
                ],
                'ctaUrl' => 'http://localhost:8001/pt/tasks/' . $this->task->id,
                'ctaLabel' => 'Visualizar Tarefa',
                'note' => 'Esta é uma notificação automática do sistema Task Force.',
                'logoUrl' => 'https://via.placeholder.com/40x40/ffffff/4f46e5?text=TF',
                'preheader' => "Nova tarefa atribuída: {$this->task->title}"
            ]);
    }

    public function toMailOld(object $notifiable): MailMessage
    {
        $url = config('app.url') . '/pt/tasks'; // URL absoluta para evitar problemas com locale
        $dueDate = $this->task->due_date ? $this->task->due_date->format('d/m/Y H:i') : 'Não definida';
        $priorityColor = $this->getPriorityColor($this->task->priority);
        $statusColor = $this->getStatusColor($this->task->status);

        return (new MailMessage)
            ->greeting("📋 Olá {$this->assignedTo->name}!")
            ->subject("🎯 Nova Tarefa Atribuída: {$this->task->title}")
            ->line("**Uma nova tarefa foi atribuída a você!**")
            ->line("")
            ->line("👤 **Atribuída por:** {$this->assignedBy->name}")
            ->line("📅 **Data de atribuição:** " . now()->format('d/m/Y H:i'))
            ->line("")
            ->line("📝 **Detalhes da Tarefa:**")
            ->line("")
            ->line("🔖 **Título:** {$this->task->title}")
            ->line("📄 **Descrição:** {$this->task->description}")
            ->line("")
            ->line("⚡ **Prioridade:** {$priorityColor} " . ucfirst($this->task->priority))
            ->line("📊 **Status:** {$statusColor} " . ucfirst($this->task->status))
            ->line("🏷️ **Categoria:** " . ($this->task->category ?: 'Não definida'))
            ->line("")
            ->when($this->task->due_date, function ($message) use ($dueDate) {
                return $message->line("⏰ **Data de Vencimento:** {$dueDate}");
            })
            ->when($this->task->estimated_hours, function ($message) {
                return $message->line("⏱️ **Horas Estimadas:** {$this->task->estimated_hours}h");
            })
            ->line("")
            ->line("🚀 **Próximos Passos:**")
            ->line("• Acesse o sistema para ver os detalhes completos")
            ->line("• Atualize o status conforme o progresso")
            ->line("• Entre em contato se tiver dúvidas")
            ->action('📋 Ver Tarefa Completa', $url)
            ->line("")
            ->line("💡 **Dica:** Mantenha o status atualizado para melhor acompanhamento!")
            ->line("")
            ->line("🎉 **Boa sorte com a nova tarefa!**")
            ->line("Obrigado por usar o Iron Force Tasks.")
            ->salutation('Atenciosamente, Equipe Iron Force');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Tarefa Atribuída',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'assigned_by_id' => $this->assignedBy->id,
            'assigned_by_name' => $this->assignedBy->name,
            'assigned_to_id' => $this->assignedTo->id,
            'assigned_to_name' => $this->assignedTo->name,
            'message' => "Tarefa '{$this->task->title}' atribuída por {$this->assignedBy->name}",
            'type' => 'task_assigned',
            'created_at' => now(),
        ];
    }
    
    /**
     * Get priority color emoji
     */
    private function getPriorityColor($priority): string
    {
        return match($priority) {
            'high' => '🔴',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪'
        };
    }
    
    /**
     * Get status color emoji
     */
    private function getStatusColor($status): string
    {
        return match($status) {
            'pending' => '⏳',
            'in_progress' => '🔄',
            'completed' => '✅',
            'cancelled' => '❌',
            default => '⚪'
        };
    }
} 