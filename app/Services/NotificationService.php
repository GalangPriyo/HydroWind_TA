<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Whatsapp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsAppService;

class NotificationService
{
    protected $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    private function parseSensorValue(string $raw): float
    {
        $clean = preg_replace('/[^0-9.]/', '', $raw);
        return is_numeric($clean) ? (float) $clean : null;
    }

    public function process(array $payload)
    {
        $nodeId = $payload['node_id'];
        $sensor = $payload['sensor'];
        $timestamp = $payload['timestamp'];

        // Ambil data device
        $device = Device::where('node_id', $nodeId)->first();
        if (!$device) {
            Log::warning("Node ID $nodeId tidak ditemukan di tabel devices.");
            return;
        }

        // Ambil dan parsing nilai sensor
        $dataSensor = [
            'curah_hujan' => [
                'label' => 'Curah Hujan',
                'satuan' => 'mm',
                'nilai' => $this->parseSensorValue($sensor['curah_hujan'] ?? '0'),
                'bahaya' => 1100,
                'waspada' => 500,
                'bencana' => 'Banjir'
            ],
            'ketinggian_air' => [
                'label' => 'Ketinggian Air',
                'satuan' => 'cm',
                'nilai' => $this->parseSensorValue($sensor['ketinggian_air'] ?? '0'),
                'bahaya' => 1200,
                'waspada' => 600,
                'bencana' => 'Banjir'
            ],
            'kecepatan_angin' => [
                'label' => 'Kecepatan Angin',
                'satuan' => 'm/s',
                'nilai' => $this->parseSensorValue($sensor['kecepatan_angin'] ?? '0'),
                'bahaya' => 1300,
                'waspada' => 700,
                'bencana' => 'Angin Kencang'
            ],
            'tekanan_udara' => [
                'label' => 'Tekanan Udara',
                'satuan' => 'hPa',
                'nilai' => $this->parseSensorValue($sensor['tekanan_udara'] ?? '0'),
                'bahaya' => 1400,
                'waspada' => 800,
                'bencana' => 'Angin Kencang'
            ],
        ];

        $pesanPerSensor = [];
        $potensiBencana = [];
        $statusGlobal = null;

        foreach ($dataSensor as $key => $info) {
            $nilai = $info['nilai'];
            if ($nilai === null) continue;

            $status = null;

            if ($nilai >= $info['bahaya']) {
                $status = 'BAHAYA';
                $statusGlobal = 'BAHAYA';
                $potensiBencana[] = $info['bencana'];
            } elseif ($nilai >= $info['waspada']) {
                $status = 'WASPADA';
                if ($statusGlobal !== 'BAHAYA') $statusGlobal = 'WASPADA';
                $potensiBencana[] = $info['bencana'];
            }

            if ($status) {
                $pesanPerSensor[] = [
                    'sensor' => $info['label'],
                    'nilai' => $nilai,
                    'satuan' => $info['satuan'],
                    'status' => $status,
                    'bencana' => $info['bencana'],
                ];
            }
        }

        if (!$statusGlobal || count($pesanPerSensor) === 0) {
            return; // Tidak ada sensor dalam status WASPADA/BAHAYA
        }

        // Cek apakah sudah mengirim notifikasi dengan status yang sama dalam 5 menit terakhir
        $cacheKey = "last_notification_{$nodeId}_{$statusGlobal}";
        $lastSent = Cache::get($cacheKey);
        $now = now();

        if ($lastSent && $now->diffInMinutes($lastSent) < 5) {
            return; // Sudah kirim dalam 5 menit terakhir, tidak perlu kirim ulang
        }

        // Simpan waktu pengiriman terakhir
        Cache::put($cacheKey, $now, now()->addMinutes(5));

        // Buat pesan notifikasi per node (walau lebih dari 1 sensor)
        $judul = "PERINGATAN BENCANA - " . strtoupper($statusGlobal);
        $message = "[$judul]\n";
        $message .= "Node ID: {$nodeId}\n";
        $message .= "Waktu: {$timestamp}\n";
        $message .= "Lokasi: {$device->location}\n\n";

        foreach ($pesanPerSensor as $item) {
            $message .= "Sensor: {$item['sensor']}\n";
            $message .= "Nilai Terukur: {$item['nilai']} {$item['satuan']}\n";
            $message .= "Status: {$item['status']}\n";
            $message .= "Bencana Potensial: {$item['bencana']}\n\n";
        }

        $message .= "Potensi bencana terdeteksi di wilayah {$device->name}. Segera waspada dan ambil tindakan pencegahan.";

        $phoneNumbers = Whatsapp::pluck('phone_number')->toArray();
        $groupIds = explode(',', env('FONNTE_GROUP_IDS', ''));
        $allTargets = array_merge($phoneNumbers, $groupIds);
        $allTargets = array_filter($allTargets, fn($target) => !empty($target));

        $this->waService->sendMessage($allTargets, $message);

        Log::info("Pesan peringatan dikirim untuk $nodeId [$statusGlobal]: $message");
    }
}
