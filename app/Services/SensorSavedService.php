<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Sensor;
use App\Models\SensorData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SensorSavedService
{
    /**
     * Simpan data dari MQTT ke database
     *
     * @param array $payload
     * @return void
     */
    public function store(array $payload): void
    {
        // Pastikan payload memiliki key yang dibutuhkan
        if (!isset($payload['node_id'], $payload['timestamp'], $payload['sensor'])) {
            Log::warning('Payload tidak lengkap', $payload);
            return;
        }

        $device = Device::where('node_id', $payload['node_id'])->first();

        if (!$device) {
            Log::warning('Device dengan node_id tidak ditemukan', ['node_id' => $payload['node_id']]);
            return;
        }

        foreach ($payload['sensor'] as $name => $rawValue) {
            // Cari sensor berdasarkan nama dan device
            $sensor = Sensor::where('device_id', $device->id)
                ->where('name', $name)
                ->first();

            if (!$sensor) {
                Log::warning('Sensor tidak ditemukan', ['device_id' => $device->id, 'sensor_name' => $name]);
                continue;
            }

            // Konversi nilai ke angka (hapus satuan seperti "cm", "mm", "m/s")
            $value = $this->sanitizeValue($rawValue);

            if (!is_numeric($value)) {
                Log::warning('Nilai sensor tidak valid', ['value' => $rawValue]);
                continue;
            }

            SensorData::create([
                'sensor_id' => $sensor->id,
                'value' => $value,
                'timestamp' => Carbon::now()->format('Y-m-d') . ' ' . $payload['timestamp'], // Gabungkan tanggal hari ini + jam dari payload
            ]);
        }
    }

    /**
     * Hapus satuan dari nilai sensor (contoh: "5m/s" menjadi 5)
     *
     * @param string $rawValue
     * @return float|null
     */
    private function sanitizeValue(string $rawValue): ?float
    {
        $clean = preg_replace('/[^0-9.]/', '', $rawValue);
        return is_numeric($clean) ? (float) $clean : null;
    }
}
