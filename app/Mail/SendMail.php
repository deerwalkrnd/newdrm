<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details=[];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        // dd($details);
        $this->details = $details;
        // dd($this->details);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->details['subject']);
        return $this->subject($this->details['subject'])
                ->view('admin.emails.sendMail');
        // return $this->view('view.name');
    }
}
