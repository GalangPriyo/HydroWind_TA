<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MonitoringController extends Controller
{
    public function index()
    {
        $devices = Device::with(['sensors.sensorData' => function ($query) {
            $query->latest();
        }])->get();

        return Inertia::render('Guest/Monitoring', ['devices' => $devices]);
    }

    public function subscribeToMQTT()
    {
        // Ambil konfigurasi dari .env
        $host = env('MQTT_BROKER_HOST');
        $port = env('MQTT_BROKER_PORT');
        $username = env('MQTT_USERNAME');
        $password = env('MQTT_PASSWORD');
        $clientId = env('MQTT_CLIENT_ID', 'laravel-client');
        $useTls = env('MQTT_TLS_ENABLED', true);

        // Daftar topik yang ingin disubscribe
        $topics = [
            'riwayat',
            'status',
            'admin',
            'peta',
        ];

        // Konfigurasi koneksi MQTT
        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls($useTls)
            ->setTlsSelfSignedAllowed(true) // Izinkan sertifikat self-signed jika perlu
            ->setTlsVerifyPeer(false);      // Nonaktifkan verifikasi peer (opsional)

        // Inisialisasi klien MQTT
        $mqtt = new MqttClient($host, $port, $clientId, MqttClient::MQTT_3_1_1);

        try {
            // Hubungkan ke broker
            $mqtt->connect($connectionSettings, true);

            echo "Berhasil terhubung ke MQTT Broker: {$host}:{$port}\n";

            // Subscribe ke beberapa topik
            foreach ($topics as $topic) {
                $mqtt->subscribe($topic, function ($topic, $message) {
                    echo "Pesan diterima di topik {$topic}: {$message}\n";
                    $this->processMQTTMessage($topic, $message);
                }, 1); // QoS 1: Minimal sekali diterima
            }

            // Loop agar koneksi tetap aktif
            $mqtt->loop(true);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        } finally {
            $mqtt->disconnect();
        }
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
