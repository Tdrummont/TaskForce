<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $creator;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $creator)
    {
        $this->task = $task;
        $this->creator = $creator;
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
        $priorityEmoji = $this->getPriorityEmoji($this->task->priority);
        $statusEmoji = $this->getStatusEmoji($this->task->status);
        
        return (new MailMessage)
            ->subject("🎯 Nova Tarefa Criada: {$this->task->title}")
            ->greeting("Olá {$notifiable->name}!")
            ->line("Você criou uma nova tarefa no sistema Iron Force Tasks.")
            ->line("")
            ->line("**Detalhes da Tarefa:**")
            ->line("📝 **Título:** {$this->task->title}")
            ->line("📋 **Descrição:** " . ($this->task->description ?: 'Sem descrição'))
            ->line("🔴 **Prioridade:** {$priorityEmoji} " . ucfirst($this->task->priority))
            ->line("⏳ **Status:** {$statusEmoji} " . ucfirst($this->task->status))
            ->line("📅 **Data de Vencimento:** " . ($this->task->due_date ? $this->task->due_date->format('d/m/Y H:i') : 'Não definida'))
            ->line("⏱️ **Horas Estimadas:** " . ($this->task->estimated_hours ?: 'Não definidas'))
            ->line("🏷️ **Categoria:** " . ($this->task->category ?: 'Sem categoria'))
            ->line("")
            ->when($this->task->tags, function ($message) {
                $tags = collect($this->task->tags)->map(fn($tag) => "#{$tag}")->join(', ');
                return $message->line("🏷️ **Tags:** {$tags}");
            })
            ->line("")
            ->line("**Criada em:** " . $this->task->created_at->format('d/m/Y H:i'))
            ->line("**Por:** {$this->creator->name}")
            ->action('Ver Tarefa', url('/tasks/' . $this->task->id))
            ->line("")
            ->line("💡 **Dica:** Você pode acompanhar o progresso desta tarefa diretamente no sistema.")
            ->salutation("Atenciosamente,\nEquipe Iron Force Tasks");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'creator_id' => $this->creator->id,
            'creator_name' => $this->creator->name,
            'priority' => $this->task->priority,
            'status' => $this->task->status,
            'due_date' => $this->task->due_date,
            'type' => 'task_created'
        ];
    }

    /**
     * Get priority emoji
     */
    private function getPriorityEmoji(string $priority): string
    {
        return match($priority) {
            'high' => '🔴',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪'
        };
    }

    /**
     * Get status emoji
     */
    private function getStatusEmoji(string $status): string
    {
        return match($status) {
            'pending' => '⏳',
            'in_progress' => '🔄',
            'completed' => '✅',
            default => '❓'
        };
    }
}
