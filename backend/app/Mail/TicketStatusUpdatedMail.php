<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $ticket) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chamado #{$this->ticket['id']} atualizado para {$this->ticket['status']}"
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-status-updated',
        );
    }
}