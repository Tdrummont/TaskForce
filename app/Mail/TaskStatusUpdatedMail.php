<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $oldStatus;
    public $newStatus;
    public $taskUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, string $oldStatus, string $newStatus)
    {
        $this->task = $task;
        $this->user = $user;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->taskUrl = config('app.url') . "/pt/tasks/{$task->id}";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔄 Status Atualizado: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tasks.status-updated',
            with: [
                'task' => $this->task,
                'user' => $this->user,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'taskUrl' => $this->taskUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
