<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Whatsapp;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class DailyReportService
{
    protected $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function sendDailyReports()
    {
        $now = Carbon::now();

        // Tentukan periode 18:00 kemarin sampai 18:00 hari ini
        if ($now->hour < 18) {
            $startTime = $now->copy()->subDay()->setTime(18, 0, 0);
            $endTime = $now->copy()->setTime(18, 0, 0);
        } else {
            $startTime = $now->copy()->setTime(18, 0, 0);
            $endTime = $now->copy()->addDay()->setTime(18, 0, 0);
        }

        Log::info("Generating daily reports for period {$startTime} to {$endTime}");

        $devices = Device::with(['sensors' => function ($query) use ($startTime, $endTime) {
            $query->with(['sensorData' => function ($q) use ($startTime, $endTime) {
                $q->whereBetween('timestamp', [$startTime, $endTime]);
            }]);
        }])->where('status', 'active')->get();

        $thresholds = [
            'curah_hujan' => ['bahaya' => 150, 'waspada' => 100],
            'ketinggian_air' => ['bahaya' => 200, 'waspada' => 150],
            'kecepatan_angin' => ['bahaya' => 50, 'waspada' => 38],
        ];

        foreach ($devices as $device) {
            if ($this->hasWarningStatus($device, $thresholds, $startTime, $endTime)) {
                Log::info("Device {$device->name} has warning status, skipping daily report");
                continue;
            }

            $reportData = $this->generateDeviceReport($device, $startTime, $endTime);

            // Hanya kirim jika ada data sensor
            if (!empty($reportData['sensors'])) {
                $this->sendDeviceReport($device, $reportData);
            }
        }
    }

    private function hasWarningStatus(Device $device, array $thresholds, Carbon $startTime, Carbon $endTime): bool
    {
        foreach ($device->sensors as $sensor) {
            $sensorName = $sensor->name;

            if (!isset($thresholds[$sensorName])) {
                continue;
            }

            // Gunakan query langsung untuk efisiensi
            $maxValue = $sensor->sensorData()
                ->whereBetween('timestamp', [$startTime, $endTime])
                ->max('value');

            if ($maxValue === null) {
                continue;
            }

            if ($maxValue >= $thresholds[$sensorName]['bahaya']) {
                return true;
            }

            if ($maxValue >= $thresholds[$sensorName]['waspada']) {
                return true;
            }
        }

        return false;
    }

    private function generateDeviceReport(Device $device, Carbon $startTime, Carbon $endTime): array
    {
        $report = [
            'device_name' => $device->name,
            'location' => $device->location,
            'period' => $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('d/m/Y H:i'),
            'sensors' => []
        ];

        foreach ($device->sensors as $sensor) {
            $dataPoints = $sensor->sensorData()
                ->whereBetween('timestamp', [$startTime, $endTime])
                ->get();

            if ($dataPoints->isEmpty()) {
                continue;
            }

            $values = $dataPoints->pluck('value')->filter()->toArray();

            // Skip jika tidak ada nilai valid
            if (empty($values)) {
                continue;
            }

            $report['sensors'][$sensor->name] = [
                'min' => min($values),
                'max' => max($values),
                'avg' => round(array_sum($values) / count($values), 2),
                'unit' => $this->getUnitForSensor($sensor->name),
                'count' => count($values)
            ];
        }

        return $report;
    }

    private function getUnitForSensor(string $sensorName): string
    {
        return match ($sensorName) {
            'curah_hujan' => 'mm',
            'ketinggian_air' => 'cm',
            'kecepatan_angin' => 'km/jam',
            'tekanan_udara' => 'hPa',
            default => ''
        };
    }

    private function sendDeviceReport(Device $device, array $reportData): void
    {
        $message = "LAPORAN HARIAN - KONDISI AMAN\n";
        $message .= "- Lokasi: {$reportData['location']}\n";
        $message .= "- Periode: {$reportData['period']}\n";
        $message .= "- Device: {$reportData['device_name']}\n\n";

        $message .= "RATA-RATA NILAI SENSOR:\n";
        foreach ($reportData['sensors'] as $sensorName => $data) {
            $label = $this->getSensorLabel($sensorName);
            $message .= "- {$label}: {$data['avg']} {$data['unit']} ";
            $message .= "(Min: {$data['min']}, Max: {$data['max']}, Data: {$data['count']})\n";
        }

        $message .= "\nSTATUS:\nAMAN - Tidak terdeteksi kondisi waspada/bahaya dalam 24 jam terakhir";

        $phoneNumbers = Whatsapp::pluck('phone_number')->toArray();
        $groupIds = explode(',', env('FONNTE_GROUP_IDS', ''));
        $allTargets = array_filter(array_merge($phoneNumbers, $groupIds));

        if (!empty($allTargets)) {
            $this->waService->sendMessage($allTargets, $message);
            Log::info("Daily report sent for device {$device->name}");
        } else {
            Log::warning("No recipients found for daily report");
        }
    }

    private function getSensorLabel(string $sensorName): string
    {
        return match ($sensorName) {
            'curah_hujan' => 'Curah Hujan',
            'ketinggian_air' => 'Ketinggian Air',
            'kecepatan_angin' => 'Kecepatan Angin',
            'tekanan_udara' => 'Tekanan Udara',
            default => ucfirst(str_replace('_', ' ', $sensorName))
        };
    }
}
