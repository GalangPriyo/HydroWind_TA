<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Battery;
use Illuminate\Support\Facades\Log;

class BatterySavedService
{
    public function store(array $payload): void
    {
        if (!isset($payload['node_id'], $payload['baterai'])) {
            Log::warning('Payload baterai tidak lengkap.');
            return;
        }

        $device = Device::where('node_id', $payload['node_id'])->first();

        if (!$device) {
            Log::warning("Device dengan node_id {$payload['node_id']} tidak ditemukan.");
            return;
        }

        Battery::updateOrCreate(
            ['device_id' => $device->id],
            [
                'level' => $payload['baterai']['level']
            ]
        );

        Log::info("Battery data updated for device {$payload['node_id']}");
    }
}
