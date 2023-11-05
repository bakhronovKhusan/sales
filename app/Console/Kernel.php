<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('hunters:before_30_min 07:30:00')->dailyAt('07:00:00');
        $schedule->command('hunters:after_15_min 07:30:00')->dailyAt('09:15:00');

        $schedule->command('hunters:before_30_min 09:00:00')->dailyAt('08:30:00');
        $schedule->command('hunters:after_15_min 09:00:00')->dailyAt('10:45:00');

        $schedule->command('hunters:before_30_min 12:00:00')->dailyAt('11:30:00');
        $schedule->command('hunters:after_15_min 12:00:00')->dailyAt('13:45:00');

        $schedule->command('hunters:before_30_min 14:00:00')->dailyAt('13:30:00');
        $schedule->command('hunters:after_15_min 14:00:00')->dailyAt('15:45:00');

        $schedule->command('hunters:before_30_min 15:30:00')->dailyAt('15:00:00');
        $schedule->command('hunters:after_15_min 15:30:00')->dailyAt('17:15:00');

        $schedule->command('hunters:before_30_min 18:30:00')->dailyAt('18:00:00');
        $schedule->command('hunters:after_15_min 18:30:00')->dailyAt('20:15:00');

        $schedule->command('hunters:before_30_min 17:00:00')->dailyAt('16:30:00');
        $schedule->command('hunters:after_15_min 17:00:00')->dailyAt('18:45:00');

        $schedule->command('hunters:before_30_min 20:00:00')->dailyAt('19:30:00');
        $schedule->command('hunters:after_15_min 20:00:00')->dailyAt('21:45:00');

        $schedule->command('hunters:before_30_min 11:00:00')->dailyAt('10:30:00');
        $schedule->command('hunters:after_15_min 11:00:00')->dailyAt('12:45:00');

        $schedule->command('hunters:before_30_min 13:00:00')->dailyAt('10:30:00');
        $schedule->command('hunters:after_15_min 13:00:00')->dailyAt('12:45:00');

        $schedule->command('hunters:before_30_min 16:00:00')->dailyAt('15:30:00');
        $schedule->command('hunters:after_15_min 16:00:00')->dailyAt('17:45:00');

        $schedule->command('hunters:before_30_min 18:00:00')->dailyAt('17:30:00');
        $schedule->command('hunters:after_15_min 18:00:00')->dailyAt('19:45:00');

        $schedule->command('hunters:before_30_min 22:00:00')->dailyAt('21:30:00');
        $schedule->command('hunters:after_15_min 22:00:00')->dailyAt('23:45:00');

        $schedule->command('hunters:before_30_min 08:30:00')->dailyAt('08:00:00');
        $schedule->command('hunters:after_15_min 08:30:00')->dailyAt('10:15:00');

        $schedule->command('hunters:before_30_min 15:00:00')->dailyAt('14:30:00');
        $schedule->command('hunters:after_15_min 15:00:00')->dailyAt('16:45:00');

        $schedule->command('hunters:before_30_min 10:00:00')->dailyAt('9:30:00');
        $schedule->command('hunters:after_15_min 10:00:00')->dailyAt('11:45:00');

        $schedule->command('hunters:before_30_min 08:00:00')->dailyAt('7:30:00');
        $schedule->command('hunters:after_15_min 08:00:00')->dailyAt('10:45:00');

        $schedule->command('hunters:before_30_min 19:00:00')->dailyAt('18:30:00');
        $schedule->command('hunters:after_15_min 19:00:00')->dailyAt('20:45:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
