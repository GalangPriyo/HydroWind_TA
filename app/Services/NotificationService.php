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

        $device = Device::where('node_id', $nodeId)->first();
        if (!$device) {
            Log::warning("Node ID $nodeId tidak ditemukan di tabel devices.");
            return;
        }

        $dataSensor = [
            'curah_hujan' => [
                'label' => 'Curah Hujan',
                'satuan' => 'mm',
                'nilai' => $this->parseSensorValue($sensor['curah_hujan'] ?? '0'),
                'bahaya' => 150,
                'waspada' => 100,
                'bencana' => 'Banjir'
            ],
            'ketinggian_air' => [
                'label' => 'Ketinggian Air',
                'satuan' => 'cm',
                'nilai' => $this->parseSensorValue($sensor['ketinggian_air'] ?? '0'),
                'bahaya' => 200,
                'waspada' => 150,
                'bencana' => 'Banjir'
            ],
            'kecepatan_angin' => [
                'label' => 'Kecepatan Angin',
                'satuan' => 'km/jam',
                'nilai' => $this->parseSensorValue($sensor['kecepatan_angin'] ?? '0'),
                'bahaya' => 50,
                'waspada' => 38,
                'bencana' => 'Angin Kencang'
            ],
        ];

        $pesanPerSensor = [];
        $statusGlobal = null;
        $currentStatus = null;

        foreach ($dataSensor as $key => $info) {
            $nilai = $info['nilai'];
            if ($nilai === null) continue;

            if ($nilai >= $info['bahaya']) {
                $currentStatus = 'BAHAYA';
                $pesanPerSensor[] = [
                    'sensor' => $info['label'],
                    'nilai' => $nilai,
                    'satuan' => $info['satuan'],
                    'status' => 'BAHAYA',
                    'bencana' => $info['bencana'],
                ];
            } elseif ($nilai >= $info['waspada']) {
                if ($currentStatus !== 'BAHAYA') {
                    $currentStatus = 'WASPADA';
                }
                $pesanPerSensor[] = [
                    'sensor' => $info['label'],
                    'nilai' => $nilai,
                    'satuan' => $info['satuan'],
                    'status' => 'WASPADA',
                    'bencana' => $info['bencana'],
                ];
            }
        }

        // Jika tidak ada status waspada/bahaya, reset semua counter
        if (!$currentStatus) {
            $this->resetAllCounters($nodeId);
            return;
        }

        $statusGlobal = $currentStatus;

        // Update status sequence
        $sequenceCacheKey = "status_sequence_{$nodeId}";
        $currentSequence = Cache::get($sequenceCacheKey, '');
        $newSequence = $currentSequence . $currentStatus[0]; // 'W' atau 'B'

        // Simpan maksimal 5 karakter terakhir
        $newSequence = substr($newSequence, -5);
        Cache::put($sequenceCacheKey, $newSequence, now()->addHours(6));

        Log::debug("Status sequence for {$nodeId}: {$newSequence}");

        // Cek kondisi pengiriman
        $shouldSend = false;

        if ($currentStatus === 'BAHAYA') {
            // Hitung berapa kali BAHAYA berturut-turut di akhir sequence
            $bahayaCount = $this->countTrailingChars($newSequence, 'B');
            $shouldSend = $bahayaCount >= 2;

            if ($shouldSend) {
                $this->sendNotification($nodeId, $device, $timestamp, $pesanPerSensor, $statusGlobal);
                $this->resetAllCounters($nodeId);
            }
        } elseif ($currentStatus === 'WASPADA') {
            // Hitung berapa kali WASPADA berturut-turut di akhir sequence
            $waspadaCount = $this->countTrailingChars($newSequence, 'W');
            $shouldSend = $waspadaCount >= 5;

            if ($shouldSend) {
                $this->sendNotification($nodeId, $device, $timestamp, $pesanPerSensor, $statusGlobal);
                $this->resetAllCounters($nodeId);
            }
        }
    }

    private function countTrailingChars(string $sequence, string $char): int
    {
        $count = 0;
        for ($i = strlen($sequence) - 1; $i >= 0; $i--) {
            if ($sequence[$i] === $char) {
                $count++;
            } else {
                break;
            }
        }
        return $count;
    }

    private function resetAllCounters(string $nodeId): void
    {
        Cache::forget("status_sequence_{$nodeId}");
        Cache::forget("BAHAYA_counter_{$nodeId}");
        Cache::forget("WASPADA_counter_{$nodeId}");
    }

    private function sendNotification(
        string $nodeId,
        Device $device,
        string $timestamp,
        array $pesanPerSensor,
        string $statusGlobal
    ): void {
        $cacheKey = "last_notification_{$nodeId}_{$statusGlobal}";
        if (Cache::get($cacheKey)) {
            return;
        }

        $judul = "PERINGATAN BENCANA - " . strtoupper($statusGlobal);
        $message = "[$judul]\nNode ID: {$nodeId}\nWaktu: {$timestamp}\nLokasi: {$device->location}\n\n";

        foreach ($pesanPerSensor as $item) {
            $message .= "Sensor: {$item['sensor']}\n";
            $message .= "Nilai Terukur: {$item['nilai']} {$item['satuan']}\n";
            $message .= "Status: {$item['status']}\n";
            $message .= "Bencana Potensial: {$item['bencana']}\n\n";
        }

        $message .= ($statusGlobal === 'BAHAYA')
            ? "PERINGATAN BAHAYA! Segera lakukan evakuasi!"
            : "Peringatan WASPADA! Waspadai potensi bencana!";

        $message .= "Potensi bencana terdeteksi di wilayah {$device->name}. Segera waspada dan ambil tindakan pencegahan.";

        $phoneNumbers = Whatsapp::pluck('phone_number')->toArray();
        $groupIds = explode(',', env('FONNTE_GROUP_IDS', ''));
        $allTargets = array_filter(array_merge($phoneNumbers, $groupIds));

        $this->waService->sendMessage($allTargets, $message);
        Cache::put($cacheKey, now(), now()->addMinutes(5));

        Log::info("Notifikasi terkirim untuk {$nodeId} [{$statusGlobal}]");
    }
}
