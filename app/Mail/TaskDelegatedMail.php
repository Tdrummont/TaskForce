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

class TaskDelegatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $delegatedBy;
    public $delegatedTo;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $delegatedBy, User $delegatedTo)
    {
        $this->task = $task;
        $this->delegatedBy = $delegatedBy;
        $this->delegatedTo = $delegatedTo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔄 Tarefa Delegada: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tasks.delegated-corporate',
            with: [
                'task' => $this->task,
                'delegatedBy' => $this->delegatedBy,
                'delegatedTo' => $this->delegatedTo,
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
