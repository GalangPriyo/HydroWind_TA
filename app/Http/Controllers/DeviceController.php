<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Support\Str;
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
            ->paginate(10)
            ->withQueryString(); // Penting agar pagination tetap membawa parameter search

        return Inertia::render('Admin/DaftarAlat/Index', [
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
        $device = Device::with('sensors')->findOrFail($id);

        return Inertia::render('Admin/DaftarAlat/Show', [
            'device' => $device,
            'user' => Auth::user(),
        ]);
    }


    // Menampilkan form tambah device
    public function createDevice()
    {
        return Inertia::render('Admin/DaftarAlat/Create', [
            'user' => Auth::user(),
        ]);
    }

    // Menyimpan device baru + sensor
    public function storeDevice(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'node_id' => 'nullable|string|max:20|unique:devices,node_id',
            'status' => 'required|in:active,inactive,maintenance',
            'sensors' => 'required|array|min:1|max:4', // 1-5 sensor
            'sensors.*.name' => 'required|in:curah_hujan,ketinggian_air,kecepatan_angin,arah_angin,tekanan_udara',
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

        // Simpan Sensor yang Dipilih
        foreach ($request->sensors as $sensor) {
            Sensor::create([
                'device_id' => $device->id,
                'name' => $sensor['name'],
            ]);
        }

        return redirect()->route('admin.devices')
            ->with('success', "Alat {$device->name} berhasil ditambahkan dengan ID: {$device->node_id}");
    }

    // Menampilkan form edit device
    public function editDevice($id)
    {
        $device = Device::with('sensors')->findOrFail($id);
        return Inertia::render('Admin/DaftarAlat/Edit', [
            'device' => $device,
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
            'sensors' => 'required|array|min:1|max:4',
            'sensors.*.id' => 'nullable|exists:sensors,id', // Jika sensor sudah ada
            'sensors.*.name' => 'required|in:curah_hujan,ketinggian_air,kecepatan_angin,arah_angin,tekanan_udara',
        ]);

        $device = Device::findOrFail($id);
        $device->update([

            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status,
        ]);

        // Update Sensor
        $existingSensorIds = [];
        foreach ($request->sensors as $sensor) {
            if (!empty($sensor['id'])) {
                // Update sensor yang sudah ada
                $existingSensor = Sensor::findOrFail($sensor['id']);
                $existingSensor->update([
                    'name' => $sensor['name'],
                ]);
                $existingSensorIds[] = $sensor['id'];
            } else {
                // Tambahkan sensor baru
                $newSensor = Sensor::create([
                    'device_id' => $device->id,
                    'name' => $sensor['name'],
                ]);
                $existingSensorIds[] = $newSensor->id;
            }
        }

        // Hapus sensor yang tidak ada dalam request
        Sensor::where('device_id', $device->id)->whereNotIn('id', $existingSensorIds)->delete();

        return redirect()->route('admin.devices')->with('success', 'Device berhasil diperbarui!');
    }

    // Menghapus device dan sensornya
    public function destroyDevice($id)
    {
        $device = Device::findOrFail($id);
        $device->delete(); // Karena ada `onDelete('cascade')`, sensor ikut terhapus

        return redirect()->route('admin.devices')->with('success', 'Device berhasil dihapus!');
    }

    public function provision(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255', // MAC address dari ESP32
        ]);

        $incomingIdentifier = $request->identifier;

        // Cek apakah device dengan identifier sementara (MAC address) sudah pernah daftar
        $device = Device::where('identifier', $incomingIdentifier)->first();

        if (!$device) {
            // Buat device baru dengan UUID sebagai identifier resmi dan token acak
            $device = Device::create([
                'identifier' => (string) Str::uuid(), // UUID sebagai identifier sebenarnya
                'token' => Str::random(32),
                'mac_address' => $incomingIdentifier, // Simpan juga MAC sebagai referensi
            ]);
        }

        return response()->json([
            'identifier' => $device->identifier,
            'token' => $device->token,
        ]);
    }
}
