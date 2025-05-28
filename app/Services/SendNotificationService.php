<?php

namespace App\Services;

use App\Models\Whatsapp;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendNotificationService
{
    protected $whatsappService;
    protected $warningMessageService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function evaluateSensorStatus(array $data): ?array
    {
        $nodeId = $data['node_id'] ?? 'unknown';
        if (!preg_match('/^Node-\d+$/', $nodeId)) {
            Log::warning("[ABORT] Node ID \"$nodeId\" tidak valid.");
            return null;
        }

        $sensor = $data['sensor'] ?? null;
        if (!$sensor) return null;

        // Batas ambang
        $batasBahaya = [
            'curah_hujan' => 1100,
            'ketinggian_air' => 1200,
            'kecepatan_angin' => 1300,
            'tekanan_udara' => 1400,
        ];

        $batasWaspada = [
            'curah_hujan' => 500,
            'ketinggian_air' => 60,
            'kecepatan_angin' => 1,
            'tekanan_udara' => 800,
        ];

        $labelSensor = [
            'curah_hujan' => 'Curah Hujan',
            'ketinggian_air' => 'Ketinggian Air',
            'kecepatan_angin' => 'Kecepatan Angin',
            'tekanan_udara' => 'Tekanan Udara',
        ];

        $status = 'aman';
        $sensorTerpicu = [];
        $nilaiSensor = [];

        foreach ($batasBahaya as $key => $batas) {
            $value = $this->extractNumericValue($sensor[$key] ?? null);
            if ($value >= $batas) {
                $status = 'bahaya';
                $sensorTerpicu[] = $key;
                $nilaiSensor[$key] = $value;
            } elseif ($value >= ($batasWaspada[$key] ?? 0)) {
                if ($status !== 'bahaya') $status = 'waspada';
                $sensorTerpicu[] = $key;
                $nilaiSensor[$key] = $value;
            }
        }

        if ($status === 'aman') return ['status' => 'aman'];

        $sensorInfo = collect($sensorTerpicu)
            ->map(function ($key) use ($labelSensor, $nilaiSensor) {
                return "{$labelSensor[$key]}: {$nilaiSensor[$key]}";
            })->implode(', ');

        return [
            'status' => $status,
            'sensor_type' => collect($sensorTerpicu)->map(fn($k) => $labelSensor[$k])->implode(', '),
            'value' => $sensorInfo,
            'node_id' => $nodeId,
        ];
    }

    private function extractNumericValue($value): float
    {
        if (is_string($value)) {
            return (float) preg_replace('/[^\d.]+/', '', $value);
        }
        return is_numeric($value) ? (float) $value : 0.0;
    }

    public function sendAlertToWhatsApp(array $alertData): array
    {
        $sensorType = $alertData['sensor_type'];
        $value = $alertData['value'];
        $nodeId = $alertData['node_id'];

        $sensorPairs = explode(',', $value);
        $message = $this->warningMessageService->generate($sensorPairs, $nodeId);

        if (!$message) {
            return ['status' => 'ok', 'message' => 'Status masih aman'];
        }

        $userNumbers = Whatsapp::pluck('phone_number')
            ->filter(fn($number) => preg_match('/^628\d{7,14}$/', $number))
            ->values()
            ->toArray();

        $groupIds = explode(',', env('FONNTE_GROUP_IDS', ''));
        $allTargets = array_filter(array_merge($userNumbers, $groupIds));

        $waService = new WhatsAppService();
        return $waService->sendMessage($allTargets, $message);
    }

    public function canSendAlert($nodeId): bool
    {
        $cacheKey = "last_alert_sent_$nodeId";
        $lastSent = Cache::get($cacheKey);

        if ($lastSent && now()->diffInMinutes($lastSent) < 10) {
            return false;
        }

        Cache::put($cacheKey, now(), now()->addMinutes(10));
        return true;
    }
}
