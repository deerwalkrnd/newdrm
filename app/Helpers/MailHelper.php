<?php
   
namespace App\Helpers;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;

use App\Http\Controllers\SendMailController;

class MailHelper{

    //Send Mail 
    public static function sendEmail($type,$collection,$subject){

        $sendMailController = new SendMailController;
        $hr = User::where('role_id','1')->with('employee:id,first_name,last_name,middle_name,email')->first();
        $from = $hr->employee->email;

        //Leave Request Mail
        if($type == '1'){
            $leave = LeaveRequest::where('id',$collection->id)
                    ->with('employee:id,first_name,middle_name,last_name,email,manager_id')
                    ->first();
        
            if($leave->employee->manager){
                $to = $leave->employee->manager->email;
                $name = $leave->employee->manager->first_name;
            }else{
                $name = $hr->employee->first_name;
                $to = $hr->employee->email;
            }
            $cc = [$leave->employee->email, $hr->employee->email]; //hr->inside colon
            $message = $leave->employee->first_name.' '.$leave->employee->middle_name.' '.$leave->employee->last_name.' has requested leave from '.$leave->start_date.' to '.$leave->end_date.'.';
            $sendMailController->sendMail($to, $from, $name, $subject, $message, $cc);
        }
        //Late Punch In mail
        else if($type = '2'){
            $attendance = Attendance::where('id',$collection->id)
                            ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                            ->first();

            $to = $attendance->employee->email;
            $name = $attendance->employee->first_name;
            
            if($attendance->employee->manager){
                $cc = [$attendance->employee->manager->email, $hr->employee->email]; 
            }else{
                $cc = [$hr->employee->email]; 
            }
            $message = 'Today you have punched in at '.$attendance->punch_in_time.'.';
            $sendMailController->sendMail($to, $from, $name, $subject, $message, $cc);
        }
    }

    //Missed Punch Out Mail
    public static function sendMissedPunchOutMail(){
        $attendances = Attendance::where('missed_punch_out','1')
                                ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                                // ->whereDate('punch_in_time',date('Y-m-d',strtotime('yesterday')))        //for cron job
                                ->get();

        $sendMailController = new SendMailController;
        // dd($attendances);
        foreach($attendances as $attendance){
            $hr = 'deena.sitikhu@deerwalk.edu.np';
            $to = $attendance->employee->email;
            if($attendance->employee->manager)
                $cc = [$attendance->employee->manager->email,$hr]; 
            else
                $cc = [$hr]; 

            $name = $attendance->employee->first_name;
            $message = 'You did not punch out yesterday';
            $regards ='HR';
            $subject = 'Missed Punch Out';
            $sendMailController->sendMail($to, $hr ,$name, $subject, $message, $cc);
        }
        // dd($employees);
        return true;
    }

}

?>