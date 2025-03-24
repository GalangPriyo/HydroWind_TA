<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Device;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $devices = Device::with(['sensors.sensorData' => function ($query) {
            $query->latest();
        }])->get();

        return Inertia::render('Guest/Monitoring', ['devices' => $devices]);
    }
}
