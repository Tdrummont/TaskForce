<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskCommentAddedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $comment;
    public $taskUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, Comment $comment)
    {
        $this->task = $task;
        $this->user = $user;
        $this->comment = $comment;
        $this->taskUrl = config('app.url') . "/pt/tasks/{$task->id}";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "💬 Novo Comentário: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tasks.comment-added',
            with: [
                'task' => $this->task,
                'user' => $this->user,
                'comment' => $this->comment,
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
