<?php

namespace App\Services;

use App\Mail\CertificateMail;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Penerbitan E-Certificate Kehadiran Otomatis.
 * Dipicu setelah peserta lolos scan validasi kehadiran (Check-in Scanner).
 * Butuh package: composer require barryvdh/laravel-dompdf
 */
class CertificateService
{
    public function generateAndSend(Transaction $transaction): bool
    {
        // Hindari mengirim sertifikat dua kali untuk transaksi yang sama
        if ($transaction->certificate_sent_at) {
            Log::info("[Certificate] Dilewati: {$transaction->order_id} sudah pernah dikirim sertifikatnya pada {$transaction->certificate_sent_at}.");
            return false;
        }

        // Fitur ini KHUSUS event bernuansa seminar/workshop yang ditandai
        // organizer lewat toggle "Terbitkan E-Sertifikat" saat membuat event.
        if (!$transaction->event || !$transaction->event->issues_certificate) {
            Log::info("[Certificate] Dilewati: event '{$transaction->event?->title}' (order {$transaction->order_id}) tidak mengaktifkan toggle 'Terbitkan E-Sertifikat'.");
            return false;
        }

        // Sertifikat KEHADIRAN: hanya untuk peserta yang benar-benar tervalidasi
        // hadir di lokasi (lolos scan check-in), bukan sekadar sudah bayar.
        if (!$transaction->isCheckedIn()) {
            Log::info("[Certificate] Dilewati: order {$transaction->order_id} belum berstatus checked-in (checkin_status: {$transaction->checkin_status}).");
            return false;
        }

        if (!class_exists(Pdf::class)) {
            Log::warning('[Certificate] Package barryvdh/laravel-dompdf belum terpasang. Jalankan: composer require barryvdh/laravel-dompdf');
            return false;
        }

        // 1) Generate & simpan PDF sertifikat. Kalau langkah ini gagal, batalkan proses
        //    (tidak ada gunanya lanjut kirim email tanpa file sertifikatnya).
        try {
            $pdf = Pdf::loadView('certificates.template', [
                'name' => $transaction->customer_name,
                'eventTitle' => $transaction->event->title,
                'eventDate' => $transaction->event->date,
                'organizationName' => $transaction->event->organization->name ?? 'AmikomEventHub',
                'orderId' => $transaction->order_id,
            ])->setPaper('a4', 'landscape');

            $fileName = 'certificates/' . $transaction->order_id . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
        } catch (\Exception $e) {
            Log::error('[Certificate] Gagal membuat PDF sertifikat: ' . $e->getMessage());
            return false;
        }

        // 2) Kirim email berisi PDF. Dipisah dari try/catch di atas supaya kalau nanti
        //    ada error lain (mis. salah nama variabel di blade, SMTP down, dsb), pesan
        //    error di log jauh lebih jelas sumbernya dari mana.
        try {
            Mail::to($transaction->customer_email)->send(
                new CertificateMail($transaction, $pdf->output())
            );
        } catch (\Exception $e) {
            Log::error('[Certificate] Gagal kirim email E-Sertifikat: ' . $e->getMessage());
            // certificate_sent_at SENGAJA tidak diisi supaya bisa dicoba kirim ulang
            // (baik lewat scan ulang, atau command certificates:send-post-event).
            return false;
        }

        $transaction->update(['certificate_sent_at' => now()]);

        return true;
    }
}