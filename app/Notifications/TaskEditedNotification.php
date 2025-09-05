<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskEditedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $editedBy,
        public User $recipient,
        public array $changes = []
    ) {}

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
        return (new MailMessage)
            ->subject("Tarefa '{$this->task->title}' foi editada")
            ->view('emails.tasks.edited-gmail', [
                'task' => $this->task,
                'editedBy' => $this->editedBy,
                'recipient' => $this->recipient,
                'changes' => $this->changes
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_edited',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'edited_by_id' => $this->editedBy->id,
            'edited_by_name' => $this->editedBy->name,
            'changes' => $this->changes,
            'message' => "A tarefa '{$this->task->title}' foi editada por {$this->editedBy->name}.",
        ];
    }

    private function getFieldDisplayName(string $field): string
    {
        return match ($field) {
            'title' => 'Título',
            'description' => 'Descrição',
            'status' => 'Status',
            'priority' => 'Prioridade',
            'due_date' => 'Data de vencimento',
            'assigned_to' => 'Responsável',
            default => ucfirst($field),
        };
    }

    private function getStatusDisplayName(string $status): string
    {
        return match ($status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluída',
            default => $status,
        };
    }

    private function getPriorityDisplayName(string $priority): string
    {
        return match ($priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => $priority,
        };
    }
}
