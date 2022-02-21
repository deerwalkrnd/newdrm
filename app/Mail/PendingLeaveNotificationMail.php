<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingLeaveNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pending Leave Notification')
                    ->view('layouts.email.pendingLeaveNotification')
                    ->with('employee',$this->employee);
    }
}
