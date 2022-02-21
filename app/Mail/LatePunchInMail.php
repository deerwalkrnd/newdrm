<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Attendance;

class LatePunchInMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $time;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Attendance $attendance)
    {
        $this->name = $attendance->employee->first_name.' '.$attendance->employee->middle_name.' '.$attendance->employee->last_name;
        $this->time = $attendance->punch_in_time;
    }
    

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Late Punch In | '.date('d M, D'))
                    ->view('layouts.email.latePunchIn')
                    ->with([
                        'name' => $this->name,
                        'time' => $this->time,
                    ]);
    }
}
