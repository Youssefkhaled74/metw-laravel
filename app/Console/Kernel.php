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
        // $schedule->command('inspire')->hourly();
        $schedule->command('cart:update-all')->everyFiveSeconds();
        $schedule->command('products:send-discount-notifications')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();

        // Cron job 1 (X working hours): auto reject overdue courier offers.
        $schedule->command('cron:run courier-auto-reject')
            ->everyMinute()
            ->name('courier-auto-reject')
            ->withoutOverlapping()
            ->runInBackground();

        // Cron job 2 (Y working hours): close expired response windows.
        $schedule->command('cron:run courier-close-response-window')
            ->everyMinute()
            ->name('courier-close-response-window')
            ->withoutOverlapping()
            ->runInBackground();

        // Cron job 3 (Z real hours): cancel unpaid advance payments (users only).
        $schedule->command('cron:run advance-payments-cancel-unpaid')
            ->everyFifteenMinutes()
            ->name('advance-payments-cancel-unpaid')
            ->withoutOverlapping()
            ->runInBackground();

        // Cron job 4 (N days): aggregate sub-shipments by receiver warehouse.
        $schedule->command('cron:run sub-shipments-aggregate')
            ->dailyAt('02:00')
            ->name('sub-shipments-aggregate')
            ->withoutOverlapping()
            ->runInBackground();

        // Cron job 5 (W real hours): mark execution start for confirmed inter-governorate requests.
        $schedule->command('cron:run shipment-requests-mark-execution-start')
            ->everyFifteenMinutes()
            ->name('shipment-requests-mark-execution-start')
            ->withoutOverlapping()
            ->runInBackground();
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
