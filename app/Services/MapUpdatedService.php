<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Log;

class MapUpdatedService
{
    public function store(array $payload): void
    {
        if (!isset($payload['node_id'], $payload['gps'])) {
            Log::warning('Payload koordinat tidak lengkap.');
            return;
        }

        $device = Device::where('node_id', $payload['node_id'])->first();

        if (!$device) {
            Log::warning("Device dengan node_id {$payload['node_id']} tidak ditemukan.");
            return;
        }

        $device->update([
            'longitude' => $payload['gps']['longitude'],
            'latitude' => $payload['gps']['latitude'],
        ]);

        Log::info("Koordinat updated untuk device {$payload['node_id']}");
    }
}
