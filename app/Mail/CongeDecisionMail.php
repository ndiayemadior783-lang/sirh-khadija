<?php

namespace App\Mail;

use App\Models\Conge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CongeDecisionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, EnvoiSmtpSecurise;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public Conge $conge)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        $decision = $this->conge->statut === 'valide' ? 'validee' : 'refusee';
        return new Envelope(subject: 'Votre demande de conge a ete ' . $decision);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.conge-decision');
    }
}
