<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\Transaction;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $pdfPath;

    public function __construct(Transaction $transaction, string $pdfPath)
    {
        $this->transaction = $transaction;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Sertifikat Keikutsertaan: ' . $this->transaction->event->title)
                    ->view('emails.certificate-notice')
                    ->with([
                        'userName'  => $this->transaction->name ?? $this->transaction->user->name,
                        'eventTitle' => $this->transaction->event->title,
                    ])
                    ->attach($this->pdfPath, [
                        'as'   => 'Sertifikat-' . $this->transaction->booking_code . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
