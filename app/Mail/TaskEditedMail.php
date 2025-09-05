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

class TaskEditedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $editedBy;
    public $recipient;
    public $changes;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $editedBy, User $recipient, array $changes = [])
    {
        $this->task = $task;
        $this->editedBy = $editedBy;
        $this->recipient = $recipient;
        $this->changes = $changes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tarefa '{$this->task->title}' foi editada",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tasks.edited',
            with: [
                'task' => $this->task,
                'editedBy' => $this->editedBy,
                'recipient' => $this->recipient,
                'changes' => $this->changes,
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
