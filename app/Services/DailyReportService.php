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

    /**
     * Entry point to generate and send the consolidated daily report.
     */
    public function sendDailyReports()
    {
        // Tentukan periode 24 jam, dari jam 18:00 kemarin sampai 18:00 hari ini.
        $endTime = Carbon::now()->setTime(18, 0, 0);
        $startTime = $endTime->copy()->subDay();

        Log::info("Memulai pembuatan Laporan Harian untuk periode: {$startTime} hingga {$endTime}");

        // Ambil semua device yang aktif beserta relasi sensornya.
        $devices = Device::with('sensors')->where('status', 'active')->get();

        if ($devices->isEmpty()) {
            Log::info("Tidak ada device aktif yang ditemukan. Laporan harian tidak dibuat.");
            return;
        }

        $allReportsData = [];
        foreach ($devices as $device) {
            // Generate data statistik untuk setiap device
            $reportData = $this->generateDeviceReportData($device, $startTime, $endTime);

            // Hanya tambahkan ke laporan jika device tersebut memiliki data sensor pada periode ini
            if (!empty($reportData['sensors'])) {
                $allReportsData[] = $reportData;
            }
        }

        // Hanya kirim laporan jika ada setidaknya satu device dengan data
        if (!empty($allReportsData)) {
            $this->buildAndSendMessage($allReportsData);
        } else {
            Log::info("Tidak ada data sensor yang ditemukan untuk device manapun pada periode ini. Laporan tidak dikirim.");
        }
    }

    /**
     * Generate statistical data for a single device within the time frame.
     */
    private function generateDeviceReportData(Device $device, Carbon $startTime, Carbon $endTime): array
    {
        $report = [
            'device_name' => $device->name,
            'node_id' => $device->node_id, // Asumsi ada kolom 'node_id' di tabel devices
            'location' => $device->location,
            'period' => $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('d/m/Y H:i'),
            'sensors' => []
        ];

        foreach ($device->sensors as $sensor) {
            $dataPoints = $sensor->sensorData()
                ->whereBetween('timestamp', [$startTime, $endTime])
                ->get();

            if ($dataPoints->isEmpty()) {
                continue; // Lanjut ke sensor berikutnya jika tidak ada data
            }

            // Ambil nilai (value) saja dan filter nilai null/kosong
            $values = $dataPoints->pluck('value')->filter(function ($value) {
                return $value !== null && $value !== '';
            })->toArray();

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

    /**
     * Build the final message string from all device reports and send it.
     */
    private function buildAndSendMessage(array $allReportsData): void
    {
        // === MEMBANGUN PESAN ===
        $message = "*LAPORAN HARIAN MONITORING - HYDROWIND*\n";
        $message .= "==============================\n";

        foreach ($allReportsData as $reportData) {
            $message .= "*INFORMASI PERANGKAT:*\n";
            $message .= "- *Nama Device:* {$reportData['device_name']}\n";
            $message .= "- *Node ID:* {$reportData['node_id']}\n";
            $message .= "- *Lokasi:* {$reportData['location']}\n";
            $message .= "- *Periode:* {$reportData['period']}\n";
            $message .= "--------------------------------------------------\n";
            $message .= "*NILAI SENSOR:*\n";

            foreach ($reportData['sensors'] as $sensorName => $data) {
                $label = $this->getSensorLabel($sensorName);
                $unit = $data['unit'];
                $message .= "- *{$label}:*\n";
                $message .= "     > Rata-rata = {$data['avg']} {$unit}\n";
                $message .= "     > Terendah = {$data['min']} {$unit}\n";
                $message .= "     > Tertinggi = {$data['max']} {$unit}\n";
                $message .= "     > Data Masuk = {$data['count']}\n";
            }
            $message .= "==============================\n\n";
        }

        // === MENGIRIM PESAN ===
        $phoneNumbers = Whatsapp::pluck('phone_number')->toArray();
        $groupIds = array_filter(explode(',', env('FONNTE_GROUP_IDS', '')));
        $allTargets = array_unique(array_merge($phoneNumbers, $groupIds));

        if (!empty($allTargets)) {
            $this->waService->sendMessage($allTargets, trim($message));
            Log::info("Laporan harian gabungan berhasil dikirim.", ['penerima' => $allTargets]);
        } else {
            Log::warning("Tidak ada penerima (nomor WA/grup ID) yang ditemukan untuk laporan harian.");
        }
    }

    /**
     * Get the unit for a given sensor name.
     */
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

    /**
     * Get a human-readable label for a given sensor name.
     */
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
