<?php

namespace App\Console;

use App\Models\Mail;

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
        Commands\MissedPunchOut::class,
        Commands\PendingLeaveNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('no:punchInLeave')->everyMinute();
        $schedule->command('leave:pending')->dailyAt('22:00');
        $send_mail = Mail::select('send_mail')->where('name','Missed Punch Out')->first()->send_mail;
        if($send_mail)
            $schedule->command('punchOut:missed')->dailyAt('23:50');
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
