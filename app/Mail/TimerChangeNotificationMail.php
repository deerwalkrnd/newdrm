<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Time;

class TimerChangeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $time;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Time $time)
    {
        $this->name = $time->name;
        $this->time = date("h:i A",strtotime($time->time));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('layouts.email.timerChangeNotification')
                    ->with([
                        'name' => $this->name,
                        'time' => $this->time,
                    ]);
    }
}
