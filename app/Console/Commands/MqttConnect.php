<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Jobs\ProcessMqttMessage;

class MqttConnect extends Command
{
    protected $signature = 'mqtt:connect';
    protected $description = 'Connect and subscribe to MQTT topics, then dispatch jobs';
    private MqttClient $mqtt;
    private array $config;

    /**
     * Handle command execution.
     */
    public function handle()
    {
        // Load configuration once
        $this->config = config('mqtt');

        // Handle shutdown signals
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
        }

        while (true) {
            try {
                // Validate required configuration
                if (empty($this->config['host'])) {
                    throw new \RuntimeException('MQTT host not configured');
                }

                $this->mqtt = new MqttClient(
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['client_id'],
                    MqttClient::MQTT_3_1
                );

                $connectionSettings = (new ConnectionSettings)
                    ->setUsername($this->config['username'])
                    ->setPassword($this->config['password'])
                    ->setUseTls($this->config['tls'])
                    ->setTlsVerifyPeer($this->config['tls_settings']['verify_peer'])
                    ->setTlsVerifyPeerName($this->config['tls_settings']['verify_peer_name'])
                    ->setKeepAliveInterval($this->config['keepalive']);

                $this->mqtt->connect($connectionSettings, true);
                $this->info("✅ Connected to MQTT broker at {$this->config['host']}:{$this->config['port']}");
                Log::info("MQTT client connected successfully", ['host' => $this->config['host']]);

                // Contoh: ['sensor', 'baterai', 'gps'] --> ada di file config/mqtt.php
                foreach ($this->config['topics'] as $topic) {
                    $this->mqtt->subscribe($topic, function (string $topic, string $message) {
                        echo "[" . now() . "] Topic: {$topic} | Dispatching job...\n";
                        Log::info("Received message on topic [{$topic}]", ['message' => $message]);

                        ProcessMqttMessage::dispatch($topic, $message);
                    }, $this->config['qos']);
                }

                $this->info("📡 Subscribed to topics: " . implode(', ', $this->config['topics']));
                $this->mqtt->loop(true);
            } catch (\Throwable $e) {
                $this->error("❌ Connection lost or error occurred: " . $e->getMessage());
                Log::error("MQTT connection error", [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $reconnectDelay = $this->config['reconnect_delay'] ?? 5;
                $this->warn("🔁 Reconnecting in {$reconnectDelay} seconds...");
                sleep($reconnectDelay);
            }
        }
    }

    /**
     * Handle shutdown signals.
     */
    public function shutdown()
    {
        $this->warn("\n🔌 Shutting down MQTT connection...");
        if (isset($this->mqtt) && $this->mqtt->isConnected()) {
            $this->mqtt->disconnect();
        }
        $this->info("Connection closed. Exiting.");
        exit(0);
    }
}
