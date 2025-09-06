<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $assignedBy;
    public $assignedTo;

    /**
     * Create a new event instance.
     */
    public function __construct($task, User $assignedBy, User $assignedTo)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
        $this->assignedTo = $assignedTo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Canal privado baseado no ID do usuário destinatário
        return [
            new PrivateChannel('user.' . $this->assignedTo->id),
        ];
    }

    /**
     * Dados que serão enviados para o frontend
     */
    public function broadcastWith(): array
    {
        return [
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'description' => $this->task->description,
                'priority' => $this->task->priority,
                'due_date' => $this->task->due_date,
                'status' => $this->task->status,
            ],
            'assigned_by' => [
                'id' => $this->assignedBy->id,
                'name' => $this->assignedBy->name,
                'email' => $this->assignedBy->email,
            ],
            'assigned_to' => [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'email' => $this->assignedTo->email,
            ],
            'message' => "Nova tarefa atribuída: {$this->task->title}",
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Nome do evento para o frontend
     */
    public function broadcastAs(): string
    {
        return 'task.assigned';
    }
}
