<?php
   
namespace App\Helpers;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingLeaveNotificationMail;
use App\Mail\MissedPunchOutMail;

class MailHelper{

    public static function getManagerEmail($id)
    {
        $employee = Employee::findOrFail($id);
        if($employee->manager != null){
            return $employee->manager->email;
        }else{
            $hr = User::whereHas('role',function($q){
                $q->where('authority','hr');
            })->with('employee:id,email')->first();

            return $hr->employee->email;
        }
    }

    public static function getHrEmail()
    {
        $hr = User::whereHas('role',function($q){
            $q->where('authority','hr');
        })
        ->whereHas('employee',function($q){
            $q->where('contract_status','active');
        })
        ->with('employee:id,email')->get()->map(function($arr){
            return $arr->employee->email;
        })->toArray();

        return $hr;
    }

    //Missed Punch Out Mail
    public static function sendMissedPunchOutMail(){

        Attendance::where('punch_out_time',NULL)->whereDate('punch_in_time',date('Y-m-d'))->update(['missed_punch_out'=>'1','punch_out_time'=>date('Y-m-d H:i:s')]);
        
        $attendances = Attendance::where('missed_punch_out','1')
                                ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                                ->whereDate('punch_in_time',date('Y-m-d'))        //for cron job
                                ->get();
        
        foreach($attendances as $attendance){
            $employee_name = $attendance->employee->first_name.' '.$attendance->employee->middle_name.' '.$attendance->employee->last_name;
            $ccList =  MailHelper::getHrEmail();
            array_push($ccList,MailHelper::getManagerEmail($attendance->employee_id));
            Mail::to($attendance->employee->email)
                ->cc($ccList)
                ->queue(new MissedPunchOutMail($employee_name));

                // ->cc(MailHelper::getManagerEmail($attendance->employee_id))
                // ->cc(MailHelper::getHrEmail())
        }
        return true;
    }


    //pending leave notification email
    public static function sendPendingLeaveMail(){
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
                                        ->with(['employee:id,first_name,last_name,manager_id,email','leaveType:id,name'])
                                        ->where('acceptance','pending')
                                        ->whereDate('start_date',date('Y-m-d',strtotime('+1 day')))
                                        ->get();
        // dd($leaveRequests);
        foreach($leaveRequests as $leaveRequest){
            $to = $leaveRequest->employee->email;
            $name = $leaveRequest->employee->first_name;
            $ccList =  MailHelper::getHrEmail();
            array_push($ccList,MailHelper::getManagerEmail($leaveRequest->employee_id));
            $mail = Mail::to($to)
                ->cc($ccList)
                ->queue(new PendingLeaveNotificationMail($name));
                
                // ->cc(MailHelper::getManagerEmail($leaveRequest->employee_id))
                // ->cc(MailHelper::getHrEmail())
        }
        return true;
    }

    public static function testMail(){
        $name = 'deena';
        Mail::to('deena.sitikhu@deerwalk.edu.np')
                ->cc(['satyadeep.neupane@deerwalk.edu.np'])
                ->queue(new PendingLeaveNotificationMail($name));
                
    }
}

?>