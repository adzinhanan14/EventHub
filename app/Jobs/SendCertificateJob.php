<?php

namespace App\Jobs;

use App\Mail\SendCertificateMail;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Ambil email tujuan
        $recipientEmail = $this->transaction->email 
            ?? $this->transaction->customer_email 
            ?? optional($this->transaction->user)->email;

        if (!$recipientEmail) {
            return;
        }

        // 2. Persiapkan data yang sesuai dengan variabel di template Blade sertifikat
        $data = [
            'name'             => $this->transaction->name 
                                  ?? $this->transaction->customer_name 
                                  ?? optional($this->transaction->user)->name 
                                  ?? 'Peserta',
            'eventTitle'       => optional($this->transaction->event)->title 
                                  ?? optional($this->transaction->event)->name 
                                  ?? 'Event',
            'eventDate'        => optional($this->transaction->event)->date 
                                  ?? optional($this->transaction->event)->start_date 
                                  ?? now(),
            'organizationName' => optional(optional($this->transaction->event)->organization)->name 
                                  ?? 'Panitia Amikom Event Hub',
            'orderId'          => $this->transaction->booking_code 
                                  ?? $this->transaction->order_id 
                                  ?? $this->transaction->id,
        ];

        // 3. Render PDF dalam bentuk Landscape (A4)
        $pdf = Pdf::loadView('pdf.certificate_template', $data)
            ->setPaper('a4', 'landscape');

        // 4. Simpan file PDF sementara di storage
        $fileName = 'certificates/cert_' . $data['orderId'] . '_' . time() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());
        $fullPath = storage_path('app/public/' . $fileName);

        // 5. Kirim Email beserta lampiran Sertifikat PDF
        Mail::to($recipientEmail)->send(new SendCertificateMail($this->transaction, $fullPath));
    }
}