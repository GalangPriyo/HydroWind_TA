<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    // Menampilkan halaman daftar device
    public function index()
    {
        $devices = Device::with('sensors')->get(); // Ambil semua device dengan sensor
        return Inertia::render('Admin/DaftarAlat/Index', [
            'devices' => $devices,
            'user' => Auth::user(),
        ]);
    }

    // Menampilkan detail device dan sensor-sensornya
    public function show($id)
    {
        $device = Device::with('sensors')->findOrFail($id);

        return Inertia::render('Admin/DaftarAlat/Show', [
            'device' => $device,
            'user' => Auth::user(),
        ]);
    }


    // Menampilkan form tambah device
    public function create()
    {
        return Inertia::render('Admin/DaftarAlat/Create', [
            'user' => Auth::user(),
        ]);
    }

    // Menyimpan device baru + sensor
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sensors' => 'required|array|min:1|max:4', // Minimal 1, maksimal 3 sensor
            'sensors.*.name' => 'required|in:rain_intensity,water_level,wind_speed,wind_direction',
            'sensors.*.unit' => 'required|string|max:10',
        ]);

        // Simpan Device
        $device = Device::create([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Simpan Sensor yang Dipilih
        foreach ($request->sensors as $sensor) {
            Sensor::create([
                'device_id' => $device->id,
                'name' => $sensor['name'],
                'unit' => $sensor['unit'],
            ]);
        }

        return redirect()->route('admin.devices')->with('success', 'Device berhasil ditambahkan!');
    }

    // Menampilkan form edit device
    public function edit($id)
    {
        $device = Device::with('sensors')->findOrFail($id);
        return Inertia::render('Admin/DaftarAlat/Edit', [
            'device' => $device,
            'user' => Auth::user(),
        ]);
    }

    // Mengupdate device dan sensor
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sensors' => 'required|array|min:1|max:4',
            'sensors.*.id' => 'nullable|exists:sensors,id', // Jika sensor sudah ada
            'sensors.*.name' => 'required|in:rain_intensity,water_level,wind_speed,wind_direction',
            'sensors.*.unit' => 'required|string|max:10',
        ]);

        $device = Device::findOrFail($id);
        $device->update([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Update Sensor
        $existingSensorIds = [];
        foreach ($request->sensors as $sensor) {
            if (!empty($sensor['id'])) {
                // Update sensor yang sudah ada
                $existingSensor = Sensor::findOrFail($sensor['id']);
                $existingSensor->update([
                    'name' => $sensor['name'],
                    'unit' => $sensor['unit'],
                ]);
                $existingSensorIds[] = $sensor['id'];
            } else {
                // Tambahkan sensor baru
                $newSensor = Sensor::create([
                    'device_id' => $device->id,
                    'name' => $sensor['name'],
                    'unit' => $sensor['unit'],
                ]);
                $existingSensorIds[] = $newSensor->id;
            }
        }

        // Hapus sensor yang tidak ada dalam request
        Sensor::where('device_id', $device->id)->whereNotIn('id', $existingSensorIds)->delete();

        return redirect()->route('admin.devices')->with('success', 'Device berhasil diperbarui!');
    }

    // Menghapus device dan sensornya
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete(); // Karena ada `onDelete('cascade')`, sensor ikut terhapus

        return redirect()->route('admin.devices')->with('success', 'Device berhasil dihapus!');
    }
}
