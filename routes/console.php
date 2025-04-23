<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mqtt:subscribe', function () {
    app(\App\Http\Controllers\MonitoringController::class)->subscribeToMQTT();
});
