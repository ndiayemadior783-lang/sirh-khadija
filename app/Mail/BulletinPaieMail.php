<?php

namespace App\Mail;

use App\Models\BulletinPaie;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulletinPaieMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public BulletinPaie $bulletin)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre bulletin de paie - ' . $this->bulletin->period);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bulletin-paie');
    }

    public function attachments(): array
    {
        $bulletin = $this->bulletin->load('employee.poste', 'employee.departement');

        return [
            Attachment::fromData(
                fn () => Pdf::loadView('exports.bulletin', ['bulletin' => $bulletin])->output(),
                'bulletin-' . $bulletin->numero . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
