<?php

namespace App\Notifications;

use App\Mail\TaskStatusUpdatedMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $user;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $user, string $oldStatus, string $newStatus)
    {
        $this->task = $task;
        $this->user = $user;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
    public function toMail(object $notifiable): TaskStatusUpdatedMail
    {
        return new TaskStatusUpdatedMail($this->task, $this->user, $this->oldStatus, $this->newStatus);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_status_updated',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "O status da tarefa '{$this->task->title}' foi alterado de '{$this->oldStatus}' para '{$this->newStatus}'.",
            'created_at' => now(),
        ];
    }
}
