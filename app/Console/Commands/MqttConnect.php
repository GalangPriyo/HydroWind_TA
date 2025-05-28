<?php

namespace App\Console\Commands;

use PhpMqtt\Client\MqttClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SensorSavedService;
use App\Services\BatterySavedService;
use App\Events\SensorMonitoringEvent;
use App\Services\SendNotificationService;
use PhpMqtt\Client\ConnectionSettings;



class MqttConnect extends Command
{
    protected $signature = 'mqtt:connect';
    protected $description = 'Connect and subscribe to multiple MQTT topics';

    public function handle()
    {
        $host = env('MQTT_BROKER_HOST');
        $port = env('MQTT_BROKER_PORT');
        $username = env('MQTT_USERNAME');
        $password = env('MQTT_PASSWORD');
        $clientId = env('MQTT_CLIENT_ID', 'laravel-client');
        $useTls = env('MQTT_TLS_ENABLED', true);
        $topics = ['sensor', 'baterai']; // Tambahkan topik lain sesuai kebutuhan

        $sensorService = new SensorSavedService();
        $notifier = app(SendNotificationService::class);
        $batteryService = new BatterySavedService();

        while (true) {
            try {
                $mqtt = new MqttClient($host, $port, $clientId, MqttClient::MQTT_3_1);
                $connectionSettings = (new ConnectionSettings)
                    ->setUsername($username)
                    ->setPassword($password)
                    ->setUseTls($useTls);

                $mqtt->connect($connectionSettings, true);
                $this->info("✅ Connected to MQTT broker at {$host}:{$port}");

                foreach ($topics as $topic) {
                    $mqtt->subscribe($topic, function (string $topic, string $message) use ($sensorService, $notifier, $batteryService) {
                        echo "[" . now() . "] Topic: {$topic} | Message: {$message}\n";

                        // Hanya proses jika topiknya adalah 'sensor'
                        if ($topic === 'sensor') {
                            $payload = json_decode($message, true);

                            if (json_last_error() === JSON_ERROR_NONE) {
                                Log::info('Processing sensor data:', $payload);

                                // 1. Simpan data sensor ke DB
                                $sensorService->store($payload);

                                // 2. Broadcast ke frontend
                                Log::info('Broadcasting sensor data...');
                                event(new SensorMonitoringEvent($payload));
                                Log::info('Broadcast completed');

                                // 3. Kirim notifikasi jika ada sensor yang tidak aman


                            } else {
                                Log::warning('JSON decoding error: ' . json_last_error_msg());
                            }
                        }

                        if ($topic === 'baterai') {
                            $payload = json_decode($message, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                Log::info('Processing battery data:', $payload);
                                $batteryService->store($payload);
                            } else {
                                Log::warning('Battery JSON decoding error: ' . json_last_error_msg());
                            }
                        }


                        // Untuk topic lain seperti gps, baterai bisa kamu tambahkan di sini juga
                    }, 0);
                }

                $this->info("📡 Subscribed to topics: " . implode(', ', $topics));
                $mqtt->loop(true);
            } catch (\Throwable $e) {
                $this->error("❌ Connection lost or error occurred: " . $e->getMessage());
            }

            $this->warn("🔁 Reconnecting in 5 seconds...");
            sleep(5);
        }
    }
}
