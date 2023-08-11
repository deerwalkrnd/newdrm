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
    protected $deerwalk_sifal;
    protected $deerwalk_compware;
    protected $deerwalk_group;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leaveList,$deerwalk_compware,$deerwalk_group,$deerwalk_sifal)
    {
        // dd($leaveList,"orgi");
        $this->leaveList = $leaveList;
        $this->deerwalk_compware = $deerwalk_compware;
        $this->$deerwalk_group = $deerwalk_group;
        $this->deerwalk_sifal = $deerwalk_sifal;
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
                        'deerwalk_sifal' => $this->deerwalk_sifal,
                        'deerwalk_compware' => $this->deerwalk_compware,
                        'deerwalk_group' => $this->deerwalk_group,
                    ]);
    }
}
