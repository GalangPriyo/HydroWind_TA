<?php

namespace App\Services;

class WarningMessageService
{
    public function generate(array $sensorPairs, string $nodeId): ?string
    {
        $status = 'aman';
        $pesanPerSensor = [];

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
