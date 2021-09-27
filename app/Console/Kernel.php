<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RecalculateLoanDues::class,
        Commands\ParMovementCompute::class,
        Commands\WordOfTheDay::class,
        Commands\CalculateAccruedInterestCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();
        $schedule->command('loan:update')->cron('01 0 * * *')->appendOutputTo(public_path('/output.txt'));
        $schedule->command('deposit:accrue')->cron('01 1 * * *')->appendOutputTo(public_path('/output.txt'));
        $schedule->command('par:calculate')->cron('00 1 * * *');
        // $schedule->command('par:calculate')->everyMinute();
        $schedule->command('holiday:implement')->cron('* * * * *')->appendOutputTo(public_path('/output.txt'));
        $schedule->command('word:day')->cron('* * * * *')->appendOutputTo(public_path('/output.txt'));
        // $schedule->command('command:test')->everyMinute();
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
