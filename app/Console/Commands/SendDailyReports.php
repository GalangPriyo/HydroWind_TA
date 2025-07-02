<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyReportService;

class SendDailyReports extends Command
{
    protected $signature = 'reports:daily';
    protected $description = 'Send daily safety reports';

    public function handle(DailyReportService $reportService)
    {
        $this->info('Sending daily reports...');
        $reportService->sendDailyReports();
        $this->info('Daily reports sent!');
    }
}
