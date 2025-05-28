<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Device;
use App\Models\Battery;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $userWithWhatsapp = User::whereHas('whatsapp')->count();

        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'active')->count();
        $inactiveDevices = Device::where('status', 'inactive')->count();
        $maintenanceDevices = Device::where('status', 'maintenance')->count();

        $batteryStatuses = Device::with('latestBattery')->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'name' => $device->name,
                'node_id' => $device->node_id,
                'level' => $device->latestBattery?->level,
                'charging' => $device->latestBattery?->charging,
                'temperature' => $device->latestBattery?->temperature,
            ];
        });

        return Inertia::render('Admin/AdminDashboard', [
            'user' => Auth::user(),
            'batteryStatuses' => $batteryStatuses,
            'statsUser' => [
                'totalUsers' => $totalUsers,
                'usersWithWhatsapp' => $userWithWhatsapp,
            ],
            'statsDevice' => [
                'totalDevices' => $totalDevices,
                'activeDevices' => $activeDevices,
                'inactiveDevices' => $inactiveDevices,
                'maintenanceDevices' => $maintenanceDevices,
            ],
        ]);
    }

    public function battryUpdate()
    {
        try {
            $devices = Device::with('latestBattery')->get();
            return response()->json($devices);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
