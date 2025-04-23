<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Device;
use App\Models\Pengguna;
use App\Models\Whatsapp;
use App\Models\SensorData;
use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpMqtt\Client\ConnectionSettings;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin
    public function dashboard()
    {
        return Inertia::render('Admin/AdminDashboard', [
            'user' => Auth::user()
        ]);
    }

    public function riwayat()
    {
        return Inertia::render('Admin/AdminRiwayat', [
            'user' => Auth::user()
        ]);
    }





    public function subscribeToMQTT()
    {
        $host = 'bac8cead2b4841e8bd7432510d2c80de.s1.eu.hivemq.cloud';
        $port = 8883; // SSL/TLS port
        $username = 'mqtt_ta';
        $password = 'Semangat_45';
        $clientId = 'laravel-subscriber';

        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls(true);

        $mqtt = new MqttClient($host, $port, $clientId);

        $mqtt->connect($connectionSettings, true);

        $mqtt->subscribe('sensor', function ($topic, $message) {
            $this->processMQTTMessage($message);
        }, 1); // QoS 1 untuk jaminan minimal sekali

        $mqtt->loop(true);
    }

    protected function processMQTTMessage($message)
    {
        $data = json_decode($message, true);

        if ($data && isset($data['node_id'], $data['sensors'])) {
            $device = Device::firstOrCreate(['node_id' => $data['node_id']]);

            foreach ($data['sensors'] as $sensorKey => $sensor) {
                SensorData::create([
                    'device_id' => $device->id,
                    'sensor_type' => $sensor['type'],
                    'value' => $sensor['value'],
                    'unit' => $sensor['unit'],
                    'latitude' => $data['gps']['latitude'] ?? null,
                    'longitude' => $data['gps']['longitude'] ?? null,
                    'status' => $data['status'] ?? 'normal',
                ]);
            }
        }
    }
}
