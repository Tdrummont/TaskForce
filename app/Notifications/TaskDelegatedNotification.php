<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDelegatedNotification extends Notification
{
    use Queueable;

    public $task;
    public $delegatedBy;
    public $delegatedTo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $delegatedBy, User $delegatedTo)
    {
        $this->task = $task;
        $this->delegatedBy = $delegatedBy;
        $this->delegatedTo = $delegatedTo;
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
        $url = route('tasks.index');

        return (new MailMessage)
            ->subject("🔄 Tarefa Delegada: {$this->task->title}")
            ->view('emails.tasks.delegated', [
                'task' => $this->task,
                'delegatedBy' => $this->delegatedBy,
                'delegatedTo' => $this->delegatedTo
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
            'title' => 'Tarefa Delegada',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'delegated_by' => $this->delegatedBy->id,
            'delegated_by_name' => $this->delegatedBy->name,
            'delegated_to' => $this->delegatedTo->id,
            'delegated_to_name' => $this->delegatedTo->name,
            'type' => 'task_delegated',
            'message' => "Tarefa '{$this->task->title}' foi delegada para você por {$this->delegatedBy->name}",
            'priority' => $this->task->priority,
            'status' => $this->task->status,
            'due_date' => $this->task->due_date?->format('Y-m-d H:i:s'),
            'category' => $this->task->category,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public static function getType(): string
    {
        return 'task_delegated';
    }

    /**
     * Get priority color for email
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
     * Get status color for email
     */
    private function getStatusColor($status): string
    {
        return match($status) {
            'pending' => '🟡',
            'in_progress' => '🔵',
            'completed' => '🟢',
            'cancelled' => '🔴',
            default => '⚪'
        };
    }
} 