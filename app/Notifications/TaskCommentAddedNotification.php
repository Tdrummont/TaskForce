<?php

namespace App\Notifications;

use App\Mail\TaskCommentAddedMail;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $user;
    public $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $user, Comment $comment)
    {
        $this->task = $task;
        $this->user = $user;
        $this->comment = $comment;
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
    public function toMail(object $notifiable): TaskCommentAddedMail
    {
        return new TaskCommentAddedMail($this->task, $this->user, $this->comment);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_comment_added',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'comment_id' => $this->comment->id,
            'comment_author' => $this->comment->user->name,
            'message' => "Um novo comentário foi adicionado à tarefa '{$this->task->title}' por {$this->comment->user->name}.",
            'created_at' => now(),
        ];
    }
}
