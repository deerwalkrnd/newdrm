<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $leaveList;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leaveList)
    {
        // dd($leaveList,"orgi");
        $this->leaveList = $leaveList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->leaveList,"const");
        return $this->subject('Employees On Leave | '.date('d M, D'))
                    ->view('layouts.email.leaveMail')
                    ->with([
                        'leaveList' => $this->leaveList,
                    ]);
    }
}
