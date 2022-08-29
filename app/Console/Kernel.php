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
        // $schedule->command('test:mail')->everyFiveMinutes(); //dailyAt('23:50')  
        $schedule->command('send:mail')->dailyAt('17:00'); //dailyAt('16:00')

        // $schedule->job(new TestJob)->everyMinute();

        //No Punch In No Leave Request Record Update
        $schedule->command('no:punchInLeave')->dailyAt('23:50'); //dailyAt('23:50') test mail

        // //Pending Leave request of tommorow Notification
        $send_mail_pending_leave_request = MailControl::select('send_mail')->where('name','Pending Leave Request')->first()->send_mail;
        if($send_mail_pending_leave_request)
            $schedule->command('leave:pending')->dailyAt('21:00');
        // ->dailyAt('21:00');
        
        // //Today's Missed Punch Out Notification 
        $send_mail_missed_punch_out = MailControl::select('send_mail')->where('name','Missed Punch Out')->first()->send_mail;
        if($send_mail_missed_punch_out)
            $schedule->command('punchOut:missed')->dailyAt('23:40');
            // ->dailyAt('23:40');


        $schedule->command('leave:mail')->dailyAt('12:46'); //dailyAt('10:00')
        
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
