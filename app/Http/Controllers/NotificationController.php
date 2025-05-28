<?php

namespace App\Http\Controllers;

use App\Models\Whatsapp;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class NotificationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Kirim pesan darurat ke semua pengguna.
     */
    public function sendEmergencyNotification(Request $request)
    {
        // Ambil semua user nomor WA dari database
        $userNumbers = Whatsapp::pluck('phone_number')
            ->filter(fn($number) => preg_match('/^628\d{7,14}$/', $number))
            ->values()
            ->toArray();

        // Ambil group ID dari .env dan ubah jadi array
        $groupIds = explode(',', env('FONNTE_GROUP_IDS', ''));

        // Gabungkan user dan grup sebagai target
        $allTargets = array_merge($userNumbers, $groupIds);

        // Filter kosong atau tidak valid (opsional)
        $allTargets = array_filter($allTargets, fn($target) => !empty($target));

        if (empty($allTargets)) {
            return response()->json(['message' => 'Tidak ada nomor atau grup yang valid.'], 400);
        }

        $nodeId = $request->input('node_id', 'Tidak diketahui');
        $sensorType = $request->input('sensor_type');
        $valueString = $request->input('value');

        if (!$sensorType || !$valueString) {
            return response()->json(['message' => 'Parameter sensor_type dan value wajib diisi.'], 422);
        }

        $message = $this->generateWarningMessage($sensorType, $valueString, $nodeId);


        if (!$message) {
            return response()->json(['message' => 'Nilai sensor masih aman, tidak perlu mengirim notifikasi.'], 200);
        }

        $response = $this->whatsappService->sendMessage($allTargets, $message);

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi dikirim ke user & grup',
            'whatsapp_response' => $response,
        ]);
    }

    private function generateWarningMessage($sensorTypes, $valueString, $nodeId)
    {
        $status = 'aman';
        $pesanPerSensor = [];

        // Konversi string nilai menjadi array
        // Misal: "Curah Hujan: 600, Ketinggian Air: 1300"
        $sensorPairs = explode(',', $valueString);

        foreach ($sensorPairs as $pair) {
            [$namaSensor, $nilai] = array_map('trim', explode(':', $pair));
            $nilai = floatval($nilai);
            $satuan = '';
            $bencana = '';
            $statusSensor = 'aman';

            switch ($namaSensor) {
                case 'Curah Hujan':
                    $satuan = 'mm';
                    if ($nilai >= 1100) {
                        $statusSensor = 'BAHAYA';
                        $bencana = 'banjir';
                    } elseif ($nilai >= 500) {
                        $statusSensor = 'WASPADA';
                        $bencana = 'banjir';
                    }
                    break;
                case 'Ketinggian Air':
                    $satuan = 'cm';
                    if ($nilai >= 1200) {
                        $statusSensor = 'BAHAYA';
                        $bencana = 'banjir';
                    } elseif ($nilai >= 600) {
                        $statusSensor = 'WASPADA';
                        $bencana = 'banjir';
                    }
                    break;
                case 'Kecepatan Angin':
                    $satuan = 'km/jam';
                    if ($nilai >= 1300) {
                        $statusSensor = 'BAHAYA';
                        $bencana = 'angin kencang';
                    } elseif ($nilai >= 1) {
                        $statusSensor = 'WASPADA';
                        $bencana = 'angin kencang';
                    }
                    break;
                case 'Tekanan Udara':
                    $satuan = 'Pa';
                    if ($nilai >= 1400) {
                        $statusSensor = 'BAHAYA';
                        $bencana = 'cuaca ekstrem';
                    } elseif ($nilai >= 800) {
                        $statusSensor = 'WASPADA';
                        $bencana = 'cuaca ekstrem';
                    }
                    break;
            }

            if ($statusSensor !== 'aman') {
                if ($statusSensor === 'BAHAYA') {
                    $status = 'bahaya';
                } elseif ($status !== 'bahaya') {
                    $status = 'waspada';
                }

                $pesanPerSensor[] = [
                    'sensor' => $namaSensor,
                    'nilai' => $nilai,
                    'status' => $statusSensor,
                    'satuan' => $satuan,
                    'bencana' => $bencana,
                ];
            }
        }

        if (empty($pesanPerSensor)) {
            return null;
        }

        // Susun pesan gabungan
        $judul = "PERINGATAN BENCANA - " . strtoupper($status);
        $message = "[$judul]\n";
        $message .= "Node ID: {$nodeId}\n\n";

        foreach ($pesanPerSensor as $item) {
            $message .= "Sensor: {$item['sensor']}\n";
            $message .= "Nilai Terukur: {$item['nilai']} {$item['satuan']}\n";
            $message .= "Status: {$item['status']} (" . ($item['status'] === 'BAHAYA' ? "Melebihi" : "Mendekati") . " ambang batas)\n\n";
        }

        $message .= "Potensi bencana terdeteksi di wilayah Node {$nodeId}. Segera waspada dan ambil tindakan pencegahan.";

        return $message;
    }
}
