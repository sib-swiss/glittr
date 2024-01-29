<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('repo:update')->dailyAt('01:00');
        $schedule->command('repo:update-authors')->dailyAt('01:30');

        $schedule->command('repo:update')->dailyAt('13:00');
        $schedule->command('repo:update-authors')->dailyAt('13:30');

        $schedule->command('cache:prune-stale-tags')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
