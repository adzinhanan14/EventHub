<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;
    public string $pdfContent;

    public function __construct(Transaction $transaction, string $pdfContent)
    {
        $this->transaction = $transaction;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Sertifikat Kehadiran: ' . $this->transaction->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.certificate-notice');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Sertifikat-' . $this->transaction->order_id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
