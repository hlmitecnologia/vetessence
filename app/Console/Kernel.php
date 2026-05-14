<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('vaccines:remind --days=7')->dailyAt('08:00');
        $schedule->command('vaccines:remind --days=3')->dailyAt('08:00');
        $schedule->command('appointments:generate-recurring')->dailyAt('03:00');
        $schedule->command('recall:process')->weekly()->mondays()->at('09:00');
        $schedule->command('birthday:process')->dailyAt('08:00');
        $schedule->command('backup:database --compress')->dailyAt('01:00');
        $schedule->command('backup:cleanup --keep=30')->dailyAt('02:00');
        $schedule->command('queue:process')->everyMinute();
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
