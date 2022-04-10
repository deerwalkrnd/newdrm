<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $nam;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        // dd($details);
        $this->name = $name;
        // dd($this->name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->details['subject']);
        // dd($this->name);
        return $this->subject('Cron Job Running')
                ->view('layouts.email.sendMail')
                ->with('name');
        // return $this->view('view.name');
    }   
}
