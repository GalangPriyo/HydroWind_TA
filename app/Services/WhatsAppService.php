<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN INI

class WhatsAppService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    public function sendMessage(array $targets, string $message)
    {
        // Pastikan token ada sebelum melanjutkan
        if (!$this->token) {
            Log::critical('FONNTE_TOKEN tidak ditemukan di file .env');
            return; // Berhenti jika token tidak ada
        }

        $results = [];

        foreach ($targets as $target) {
            try {
                $response = Http::asForm()->withHeaders([
                    'Authorization' => $this->token
                ])->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                    'delay' => 1,
                    'countryCode' => '62',
                ]);

                // === LOGGING KRUSIAL ===
                // Catat semua respons dari Fonnte, baik berhasil maupun gagal
                Log::info('Respons dari Fonnte API untuk target: ' . $target, [
                    'status_code' => $response->status(),
                    'body' => $response->json() // atau ->body() jika respons bukan JSON
                ]);

                // Tambahkan pengecekan jika Fonnte mengembalikan status gagal
                if ($response->failed()) {
                    Log::error('Fonnte API mengembalikan status error.', [
                        'target' => $target,
                        'response' => $response->json()
                    ]);
                }

                $results[$target] = $response->json();
            } catch (\Exception $e) {
                // === LOGGING JIKA KONEKSI GAGAL ===
                // Catat jika ada masalah saat menghubungi server Fonnte
                Log::critical('Gagal koneksi ke Fonnte API.', [
                    'target' => $target,
                    'error' => $e->getMessage()
                ]);
                $results[$target] = ['error' => 'Connection failed', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }
}
