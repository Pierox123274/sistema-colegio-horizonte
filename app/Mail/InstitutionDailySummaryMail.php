<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstitutionDailySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $metrics
     */
    public function __construct(
        public array $metrics
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resumen diario institucional — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.institution-daily-summary',
        );
    }
}
