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
        $devices = Device::with(['sensors.latestData'])
            ->where('status', 'active')
            ->whereNotNull('node_id')
            ->get();

        return Inertia::render('Guest/Monitoring', [
            'registeredNodeIds' => $devices->pluck('node_id')->all(),
            'initialDevicesData' => $devices->map(function ($device) {
                $timestamp = $device->sensors->first()?->latestData?->timestamp
                    ? $device->sensors->first()->latestData->timestamp->format('d-m-Y | H:i:s') . ' WIB'
                    : 'Tidak tersedia';
                return [
                    'node_id' => $device->node_id,
                    'name' => $device->name,
                    'location' => $device->location,
                    'status' => 'Offline',
                    'last_updated' => $timestamp,
                    'sensors' => $device->sensors->map(function ($sensor) {
                        return [
                            'type' => $sensor->name,
                            'value' => $sensor->latestData->value ?? 0,
                            'data' => array_fill(0, 10, $sensor->latestData->value ?? 0),
                        ];
                    })
                ];
            })
        ]);
    }

    public function map(): Response
    {
        $devices = Device::with(['sensors.latestData'])
            ->where('status', 'active')
            ->get(['id', 'node_id', 'name', 'location', 'latitude', 'longitude']);

        return Inertia::render('Guest/Peta', [
            'devices' => $devices,
        ]);
    }
}
