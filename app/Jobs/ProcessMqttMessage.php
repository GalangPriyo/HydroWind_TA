<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\SensorSavedService;
use App\Services\BatterySavedService;
use App\Services\MapUpdatedService;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use App\Events\SensorMonitoringEvent;
use App\Events\MapMonitoringEvent;

class ProcessMqttMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $topic;
    protected string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $topic, string $message)
    {
        $this->topic = $topic;
        $this->message = $message;
    }

    /**
     * Execute the job.
     * Semua logika pemrosesan pesan dipindahkan ke sini.
     */
    public function handle(): void
    {
        Log::info("Processing job for topic [{$this->topic}]");

        $payload = json_decode($this->message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("JSON decoding error in job: " . json_last_error_msg(), [
                'topic' => $this->topic,
            ]);
            return;
        }

        try {
            if ($this->topic === 'sensor') {
                $sensorService = new SensorSavedService();
                $whatsappService = new WhatsAppService();
                $notificationService = new NotificationService($whatsappService);

                // 1. Simpan data sensor ke DB
                $sensorService->store($payload);

                // 2. Broadcast ke frontend
                event(new SensorMonitoringEvent($payload));

                // 3. Kirim notifikasi WhatsApp
                $notificationService->process($payload);
            }

            if ($this->topic === 'baterai') {
                $batteryService = new BatterySavedService();
                $batteryService->store($payload);
            }

            if ($this->topic === 'gps') {
                $mapService = new MapUpdatedService();

                // 1. Simpan data GPS
                $mapService->store($payload);

                // 2. Broadcast ke frontend
                event(new MapMonitoringEvent($payload));
            }
        } catch (\Throwable $e) {
            Log::critical("Error processing job for topic [{$this->topic}]", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Melempar kembali error agar job bisa di-retry jika dikonfigurasi
            $this->fail($e);
        }
    }
}
