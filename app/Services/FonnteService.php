<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi notifikasi WhatsApp memakai API Fonnte (https://fonnte.com).
 * Dipakai untuk: kirim E-Ticket via WA & Abandoned Cart Recovery.
 */
class FonnteService
{
    protected ?string $token;
    protected bool $enabled;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->enabled = config('services.fonnte.enabled', false) && !empty($this->token);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     * Nomor otomatis dirapikan ke format 62xxxxxxxxxx.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->isEnabled()) {
            Log::info('[Fonnte] WA dilewati (belum dikonfigurasi). Pesan: ' . $message);
            return false;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'  => $this->normalizePhone($phone),
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::error('[Fonnte] Gagal kirim WA: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[Fonnte] Exception saat kirim WA: ' . $e->getMessage());
            return false;
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
