<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\LeaveRequest;
use App\Models\User;

class SubOrdinateLeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $employee;
    protected $manager;
    protected $start_date;
    protected $end_date;
    protected $days;
    protected $leave_type;
    protected $requested_by;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        // dd($leaveRequest);
        $this->employee = $leaveRequest->employee->first_name . ' ' . $leaveRequest->employee->middle_name. ' ' . $leaveRequest->employee->last_name;
        if($leaveRequest->employee->manager != null){
            $this->manager = $leaveRequest->employee->manager->first_name . ' ' . $leaveRequest->employee->manager->middle_name. ' ' . $leaveRequest->employee->manager->last_name;
        }else{
            $hr = User::whereHas('role',function($q){
                $q->where('authority','hr');
            })->with('employee:id,first_name,last_name,middle_name,email')->first();
 
            $this->manager = $hr->employee->first_name.' '.$hr->employee->middle_name.' '.$hr->employee->last_name;
        }

        $this->start_date = $leaveRequest->start_date;
        $this->end_date = $leaveRequest->end_date;
        $this->days = $leaveRequest->days;
        $this->leave_type = $leaveRequest->leaveType->name;
        $this->leave_half = $leaveRequest->full_leave == 1? 'Full' : ucfirst($leaveRequest->half_leave)." Half";
        $this->requested_by = $leaveRequest->requested_by_detail->first_name . ' ' . $leaveRequest->requested_by_detail->middle_name. ' ' . $leaveRequest->requested_by_detail->last_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('layouts.email.subOrdinateLeave')->with([
            'employee' => $this->employee, 
            'manager' => $this->manager, 
            'start_date' => $this->start_date, 
            'end_date' => $this->end_date, 
            'days' => $this->days, 
            'leave_type' => $this->leave_type, 
            'leave_half' => $this->leave_half,
            'requested_by' => $this->requested_by
        ]);
    }
}
