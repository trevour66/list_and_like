<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ScrapeInstagramProfiles;
use App\Jobs\AnalyzeIGData;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ScrapeInstagramProfiles)->everyTenMinutes()->withoutOverlapping();
        // $schedule->job(new AnalyzeIGData)->everyTenMinutes()->withoutOverlapping();
        $schedule->job(new AnalyzeIGData)->everySixHours()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
