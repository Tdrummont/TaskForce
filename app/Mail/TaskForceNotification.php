<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskForceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $title,
        public ?string $intro = null,
        public ?array $highlights = null,
        public ?array $infoItems = null,
        public ?string $ctaUrl = null,
        public ?string $ctaLabel = 'Visualizar Tarefa',
        public ?string $note = null,
        public ?string $logoUrl = null,
        public ?string $preheader = null,
    ) {}

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.taskforce.notification')
            ->with([
                'subject'    => $this->subjectLine,
                'title'      => $this->title,
                'intro'      => $this->intro,
                'highlights' => $this->highlights,
                'infoItems'  => $this->infoItems,
                'ctaUrl'     => $this->ctaUrl,
                'ctaLabel'   => $this->ctaLabel,
                'note'       => $this->note,
                'logoUrl'    => $this->logoUrl,
                'preheader'  => $this->preheader,
            ]);
    }
}
