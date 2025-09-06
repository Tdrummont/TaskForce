<?php

namespace App\Events;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $comment;
    public $commentedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, TaskComment $comment, User $commentedBy)
    {
        $this->task = $task;
        $this->comment = $comment;
        $this->commentedBy = $commentedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->task->user_id), // Usuário responsável pela tarefa
        ];

        // Se a tarefa foi criada por outro usuário, notificar também
        if ($this->task->created_by && $this->task->created_by !== $this->task->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->task->created_by);
        }

        // Se quem comentou não é nem o responsável nem o criador, notificar também
        if ($this->commentedBy->id !== $this->task->user_id && 
            $this->commentedBy->id !== $this->task->created_by) {
            $channels[] = new PrivateChannel('user.' . $this->commentedBy->id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'status' => $this->task->status,
            ],
            'comment' => [
                'id' => $this->comment->id,
                'content' => $this->comment->content,
                'created_at' => $this->comment->created_at->toISOString(),
            ],
            'commented_by' => [
                'id' => $this->commentedBy->id,
                'name' => $this->commentedBy->name,
                'email' => $this->commentedBy->email,
            ],
            'message' => "Novo comentário adicionado na tarefa '{$this->task->title}' por {$this->commentedBy->name}",
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'task.comment_added';
    }
}
