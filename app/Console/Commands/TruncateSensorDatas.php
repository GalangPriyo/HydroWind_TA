<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateSensorDatas extends Command
{
    protected $signature = 'sensor_datas:truncate';
    protected $description = 'Truncate the sensor_datas table every month';

    public function handle()
    {
        DB::table('sensor_datas')->truncate();
        $this->info('sensor_datas table truncated successfully.');
    }
}
