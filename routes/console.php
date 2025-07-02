<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sensor_datas:truncate')
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Jakarta'); // WIB (GMT+7)

Schedule::command('reports:daily')
    ->dailyAt('18:05') // Jalankan setiap jam 18:05
    ->timezone('Asia/Jakarta');
