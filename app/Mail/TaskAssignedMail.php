<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $assignedBy;
    public $assignedTo;

    /**
     * Create a new message instance.
     */
    public function __construct($task, User $assignedBy, User $assignedTo)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
        $this->assignedTo = $assignedTo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nova Tarefa Atribuída: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.assigned',
            with: [
                'task' => $this->task,
                'assignedBy' => $this->assignedBy,
                'assignedTo' => $this->assignedTo,
                'taskUrl' => route('tasks.index'), // Usar rota que existe
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
