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

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $createdBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, User $createdBy)
    {
        $this->task = $task;
        $this->createdBy = $createdBy;
        
        \Log::info('🎯 TaskCreated event constructor called', [
            'task_id' => $task->id,
            'created_by' => $createdBy->id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->createdBy->id), // Criador da tarefa
        ];

        // Se a tarefa foi atribuída a outro usuário, notificar também
        if ($this->task->assigned_to && $this->task->assigned_to !== $this->createdBy->id) {
            $channels[] = new PrivateChannel('user.' . $this->task->assigned_to);
        }

        \Log::info('🎯 TaskCreated broadcastOn called', [
            'channels' => array_map(fn($channel) => $channel->name, $channels),
            'task_id' => $this->task->id,
            'created_by' => $this->createdBy->id
        ]);

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
                'description' => $this->task->description,
                'priority' => $this->task->priority,
                'status' => $this->task->status,
                'category' => $this->task->category,
                'due_date' => $this->task->due_date?->format('Y-m-d H:i:s'),
                'estimated_hours' => $this->task->estimated_hours,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'assigned_to' => $this->task->assigned_to ? [
                'id' => $this->task->assigned_to,
                'name' => $this->task->assignedTo->name ?? 'Usuário não encontrado',
            ] : null,
            'message' => "Nova tarefa criada: '{$this->task->title}'",
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'task.created';
    }
}
