<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $updatedBy;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, User $updatedBy, string $oldStatus, string $newStatus)
    {
        $this->task = $task;
        $this->updatedBy = $updatedBy;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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

        // Se quem atualizou não é nem o responsável nem o criador, notificar também
        if ($this->updatedBy->id !== $this->task->user_id && 
            $this->updatedBy->id !== $this->task->created_by) {
            $channels[] = new PrivateChannel('user.' . $this->updatedBy->id);
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
                'status' => $this->newStatus,
                'old_status' => $this->oldStatus,
                'priority' => $this->task->priority,
                'due_date' => $this->task->due_date?->format('Y-m-d H:i:s'),
            ],
            'updated_by' => [
                'id' => $this->updatedBy->id,
                'name' => $this->updatedBy->name,
                'email' => $this->updatedBy->email,
            ],
            'message' => "Status da tarefa '{$this->task->title}' alterado de '{$this->oldStatus}' para '{$this->newStatus}' por {$this->updatedBy->name}",
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'task.status_updated';
    }
}
