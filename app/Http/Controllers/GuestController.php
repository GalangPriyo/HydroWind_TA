<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{

    public function home(): Response
    {
        $devices = Device::with(['sensors' => function ($query) {
            $query->with(['latestData']);
        }])
            ->where('status', 'active')
            ->get();

        return Inertia::render('Guest/Welcome', [
            'devices' => $devices,
        ]);
    }


    public function panduan(): Response
    {
        $devices = Device::with('sensors')->get();
        return Inertia::render('Guest/Panduan', ['devices' => $devices]);
    }

    public function monitoring()
    {
        $devices = Device::with(['sensors' => function ($query) {
            $query->with(['latestData']);
        }])
            ->where('status', 'active')
            ->whereNotNull('node_id')
            ->get();

        return Inertia::render('Guest/Monitoring', [
            'registeredNodeIds' => $devices->pluck('node_id')->all(),
            'initialDevicesData' => $devices->map(function ($device) {
                return [
                    'node_id' => $device->node_id,
                    'name' => $device->name, // Tambahkan nama device
                    'location' => $device->location, // Tambahkan lokasi
                    'sensors' => $device->sensors->map(function ($sensor) {
                        return [
                            'type' => $sensor->name, // Di database fieldnya 'name' bukan 'type'
                            'value' => $sensor->latestData->value ?? 0,
                            'unit' => $this->getSensorUnit($sensor->name), // Tambahkan method untuk menentukan unit
                            'data' => array_fill(0, 10, $sensor->latestData->value ?? 0),
                            'timestamp' => $sensor->latestData->timestamp ?? now()->toDateTimeString()
                        ];
                    })
                ];
            })
        ]);
    }

    // Tambahkan method baru untuk menentukan unit sensor
    private function getSensorUnit($sensorName)
    {
        $units = [
            'curah_hujan' => 'mm',
            'ketinggian_air' => 'cm',
            'kecepatan_angin' => 'm/s',
            'tekanan_udara' => 'hPa'
        ];

        return $units[$sensorName] ?? 'N/A';
    }

    public function map(): Response
    {
        $devices = Device::where('status', 'active')->get(['node_id', 'name', 'location', 'latitude', 'longitude']);

        return Inertia::render('Guest/Peta', [
            'devices' => $devices,
        ]);
    }
}
