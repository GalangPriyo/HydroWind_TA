<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Inertia\Inertia;
use Inertia\Response;

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

    public function monitoring(): Response
    {
        return Inertia::render('Guest/Monitoring');
    }

    public function showMap(): Response
    {
        $devices = Device::select('id', 'name', 'latitude', 'longitude')->get();

        return Inertia::render('Guest/Peta', [
            'devices' => $devices,
        ]);
    }
}
