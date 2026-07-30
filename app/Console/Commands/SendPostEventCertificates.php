<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\CertificateService;
use Illuminate\Console\Command;

/**
 * E-Certificate Otomatis (Fitur Primadona Seminar/Workshop).
 *
 * Sertifikat sebenarnya SUDAH dikirim real-time begitu peserta lolos
 * scan check-in (lihat CheckinController::scan() -> CertificateService).
 * Command ini adalah JARING PENGAMAN: begitu acara ditandai berakhir,
 * command ini menyisir SEMUA peserta yang tervalidasi hadir tapi entah
 * kenapa belum menerima sertifikat (mis. email server sempat down saat
 * scan, atau panitia sempat scan luring/offline lalu baru sinkron nanti).
 */
class SendPostEventCertificates extends Command
{
    protected $signature = 'certificates:send-post-event';

    protected $description = 'Kirim E-Sertifikat kehadiran untuk event seminar/workshop yang baru saja berakhir (jaring pengaman selain trigger real-time saat scan check-in)';

    public function handle(CertificateService $certificateService): int
    {
        // Event seminar/workshop yang sudah lewat tanggalnya, tapi tidak terlalu lama
        // (dibatasi 3 hari) supaya command ini tidak menyisir habis riwayat event lama
        // tiap kali dijalankan.
        $events = Event::where('issues_certificate', true)
            ->whereNotNull('date')
            ->where('date', '<=', now())
            ->where('date', '>=', now()->subDays(3))
            ->with(['transactions' => function ($query) {
                $query->where('status', 'Success')
                    ->where('checkin_status', 'used')
                    ->whereNull('certificate_sent_at');
            }])
            ->get();

        $sentCount = 0;
        $checkedCount = 0;

        foreach ($events as $event) {
            foreach ($event->transactions as $transaction) {
                $checkedCount++;

                if ($certificateService->generateAndSend($transaction)) {
                    $sentCount++;
                    $this->line("✔ Sertifikat terkirim: {$transaction->customer_name} ({$transaction->order_id}) - {$event->title}");
                }
            }
        }

        $this->info("Selesai. {$sentCount} dari {$checkedCount} sertifikat berhasil dikirim, dari {$events->count()} event yang baru berakhir.");

        return self::SUCCESS;
    }
}
