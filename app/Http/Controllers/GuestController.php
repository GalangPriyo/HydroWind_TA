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
        return Inertia::render('Guest/Welcome');
    }

    public function panduan(): Response
    {
        return Inertia::render('Guest/Panduan');
    }

    public function monitoring()
    {

        $nodeIds = \App\Models\Device::where('status', 'active')
            ->whereNotNull('node_id')
            ->pluck('node_id')
            ->all();
        return Inertia::render('Guest/Monitoring', ['registeredNodeIds' => $nodeIds]);
    }

    public function showMap(): Response
    {
        $devices = Device::select('id', 'name', 'latitude', 'longitude')->get();

        return Inertia::render('Guest/Peta', [
            'devices' => $devices,
        ]);
    }
}
