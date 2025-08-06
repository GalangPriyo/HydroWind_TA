<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Device;
use App\Models\Sensor;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    // Menampilkan halaman daftar device
    public function indexDevice(Request $request)
    {
        $search = $request->input('search');

        $devices = Device::with('sensors')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('node_id', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->paginate(10)->withQueryString(); // Penting agar pagination tetap membawa parameter search



        return Inertia::render('Admin/Device/Index', [
            'devices' => $devices,
            'filters' => $request->only(['search']),
            'user' => Auth::user(),
            'stats' => [
                'totalDevices' => Device::count(),
                'activeDevices' => Device::where('status', 'active')->count(),
                'inactiveDevices' => Device::where('status', 'inactive')->count(),
                'maintenanceDevices' => Device::where('status', 'maintenance')->count(),
            ]

        ]);
    }

    // Menampilkan detail device dan sensor-sensornya
    public function showDevice($id)
    {
        $device = Device::with('sensors.threshold')->findOrFail($id);

        return Inertia::render('Admin/Device/Show', [
            'device' => $device,
            'user' => Auth::user(),
        ]);
    }


    // Menampilkan form tambah device
    public function createDevice()
    {
        $defaultThresholds = [
            'curah_hujan' => ['waspada' => 100, 'bahaya' => 150],
            'ketinggian_air' => ['waspada' => 120, 'bahaya' => 150],
            'kecepatan_angin' => ['waspada' => 38, 'bahaya' => 50],
            'tekanan_udara' => ['waspada' => 0, 'bahaya' => 1],
        ];

        return Inertia::render('Admin/Device/Create', [
            'user' => Auth::user(),
            'defaultThresholds' => $defaultThresholds,
        ]);
    }

    // Menyimpan device baru + sensor + threshold
    public function storeDevice(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'node_id' => 'required|string|max:20|unique:devices,node_id',
            'status' => 'required|in:active,inactive,maintenance',
            'sensors' => 'required|array|min:1|max:4',
            'sensors.*.name' => 'required|in:curah_hujan,ketinggian_air,kecepatan_angin,arah_angin,tekanan_udara',
            'thresholds' => 'required|array',
            'thresholds.*.waspada' => 'required|numeric|min:0',
            'thresholds.*.bahaya' => 'required|numeric|gt:thresholds.*.waspada',
        ]);

        // Simpan Device
        $device = Device::create([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'node_id' => $request->node_id,
            'status' => $request->status,
        ]);

        // Simpan Sensor dan Threshold
        foreach ($request->sensors as $index => $sensor) {
            $sensorModel = Sensor::create([
                'device_id' => $device->id,
                'name' => $sensor['name'],
            ]);

            // Simpan threshold untuk sensor ini
            Threshold::create([
                'sensor_id' => $sensorModel->id,
                'waspada' => $request->thresholds[$index]['waspada'],
                'bahaya' => $request->thresholds[$index]['bahaya'],
            ]);
        }

        return redirect()->route('admin.devices')
            ->with('success', "Alat {$device->name} berhasil ditambahkan dengan ID: {$device->node_id}");
    }

    // Menampilkan form edit device
    public function editDevice($id)
    {
        $device = Device::with(['sensors', 'sensors.threshold'])->findOrFail($id);

        $defaultThresholds = [
            'curah_hujan' => ['waspada' => 100, 'bahaya' => 150],
            'ketinggian_air' => ['waspada' => 120, 'bahaya' => 150],
            'kecepatan_angin' => ['waspada' => 38, 'bahaya' => 50],
            'tekanan_udara' => ['waspada' => 0, 'bahaya' => 1],
        ];

        // Format thresholds untuk frontend
        $thresholds = [];
        foreach ($device->sensors as $sensor) {
            $thresholds[$sensor->name] = $sensor->threshold
                ? ['waspada' => $sensor->threshold->waspada, 'bahaya' => $sensor->threshold->bahaya]
                : $defaultThresholds[$sensor->name] ?? ['waspada' => 0, 'bahaya' => 0];
        }

        return Inertia::render('Admin/Device/Edit', [
            'device' => $device,
            'thresholds' => $thresholds,
            'defaultThresholds' => $defaultThresholds,
            'user' => Auth::user(),
        ]);
    }

    // Mengupdate device dan sensor
    public function updateDevice(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,maintenance',
            'sensors' => 'required|array|min:1|max:4',
            'sensors.*.id' => 'nullable|exists:sensors,id',
            'sensors.*.name' => 'required|in:curah_hujan,ketinggian_air,kecepatan_angin,arah_angin,tekanan_udara',
            'thresholds' => 'required|array',
            'thresholds.*.waspada' => 'required|numeric|min:0',
            'thresholds.*.bahaya' => 'required|numeric|gt:thresholds.*.waspada',
        ]);

        $device = Device::findOrFail($id);
        $device->update([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status,
        ]);

        $existingSensorIds = [];

        foreach ($request->sensors as $index => $sensor) {
            if (!empty($sensor['id'])) {
                // Update existing sensor
                $existingSensor = Sensor::findOrFail($sensor['id']);
                $existingSensor->update(['name' => $sensor['name']]);
                $existingSensorIds[] = $sensor['id'];

                // Update threshold
                Threshold::updateOrCreate(
                    ['sensor_id' => $sensor['id']],
                    [
                        'waspada' => $request->thresholds[$index]['waspada'],
                        'bahaya' => $request->thresholds[$index]['bahaya'],
                    ]
                );
            } else {
                // Check if sensor exists for this device
                $existingSensor = Sensor::where('device_id', $device->id)
                    ->where('name', $sensor['name'])
                    ->first();

                if ($existingSensor) {
                    $existingSensorIds[] = $existingSensor->id;

                    // Update threshold for existing sensor
                    Threshold::updateOrCreate(
                        ['sensor_id' => $existingSensor->id],
                        [
                            'waspada' => $request->thresholds[$index]['waspada'],
                            'bahaya' => $request->thresholds[$index]['bahaya'],
                        ]
                    );
                } else {
                    // Create new sensor
                    $newSensor = Sensor::create([
                        'device_id' => $device->id,
                        'name' => $sensor['name'],
                    ]);
                    $existingSensorIds[] = $newSensor->id;

                    // Create threshold for new sensor
                    Threshold::create([
                        'sensor_id' => $newSensor->id,
                        'waspada' => $request->thresholds[$index]['waspada'],
                        'bahaya' => $request->thresholds[$index]['bahaya'],
                    ]);
                }
            }
        }

        // Delete sensors not in request
        Sensor::where('device_id', $device->id)
            ->whereNotIn('id', $existingSensorIds)
            ->delete();

        return redirect()->route('admin.devices')
            ->with('success', 'Device berhasil diperbarui!');
    }

    // Menghapus device dan sensornya
    public function destroyDevice($id)
    {
        $device = Device::findOrFail($id);

        // Check if device is active
        if ($device->status === 'active') {
            return redirect()->route('admin.devices')->with('error', 'Device aktif tidak dapat dihapus!');
        }

        $device->delete(); // Karena ada `onDelete('cascade')`, sensor ikut terhapus

        return redirect()->route('admin.devices')->with('success', 'Device berhasil dihapus!');
    }
}
