<?php

namespace App\Console;

use App\Models\MailControl;
use App\Jobs\TestJob;
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
        Commands\MissedPunchOutMail::class,
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
        // Mail cron job set to check daily status of cron job and queue
        $schedule->command('send:mail')->dailyAt('16:00'); //dailyAt('16:00')

        $schedule->command('no:punchInLeave')->dailyAt('23:50'); //dailyAt('23:50') test mail

        if (self::mailFlagOn('Pending Leave Request')) {
            $schedule->command('leave:pending')->dailyAt('21:00');
        }

        if (self::mailFlagOn('Missed Punch Out Full Functionality')) {
            $schedule->command('punchOut:missed')
                ->dailyAt('23:20')
                ->appendOutputTo(storage_path('logs/scheduler.log'));

            if (self::mailFlagOn('Missed Punch Out Mail')) {
                $schedule->command('missedPunchOut:mail')
                    ->dailyAt('23:40')
                    ->appendOutputTo(storage_path('logs/scheduler.log'));
            }
        }

        // Morning Leave Mail Schedule
        // $schedule->command('leave:mail')->dailyAt('10:00'); //dailyAt('10:00')
        
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

    private static function mailFlagOn($name)
    {
        return MailControl::where('name', $name)->value('send_mail') == '1';
    }
}
