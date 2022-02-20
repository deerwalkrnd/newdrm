<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCredentialMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $employee_id;
    protected $username;
    protected $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->name = $user->employee->first_name.' '.$user->employee->middle_name.' '.$user->employee->last_name;
        $this->employee_id = $user->employee->employee_id;
        $this->username = $user->username;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('layouts.email.employeeCredential')->with([
            'username' => $this->username,
            'employee_id' => $this->employee_id,
            'name' => $this->name
        ]);
    }
}
