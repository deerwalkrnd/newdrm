<?php
   
namespace App\Helpers;
use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Controllers\SendMailController;

class MailHelper{

    // private $hr = 'deenasitikhu@gmail.com';

    public static function getEmail(){
        
        $email['employee_fullname'] = \Auth::user()->employee->first_name.' '.\Auth::user()->employee->middle_name.' '.\Auth::user()->employee->last_name;
        $email['manager'] =  \Auth::user()->employee->manager->email;
        $email['employee'] = \Auth::user()->employee->email;
        $email['hr'] = 'satyadeep.neupane@deerwalk.edu.np';
        return $email;
        // dd($this->hr);
    }

    public static function sendMissedPunchOutMail(){
        $attendances = Attendance::where('missed_punch_out','1')
                                ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                                ->whereDate('punch_in_time',date('Y-m-d',strtotime('yesterday')))
                                ->get();
        // return($employees);

        $sendMailController = new SendMailController;

        foreach($attendances as $attendance){
            // dd($attendance->employee->first_name);
            $hr = 'satyadeep.neupane@deerwalk.edu.np';
            $to = $attendance->employee->email;
            $cc = [$attendance->employee->manager->email,$hr]; //hr->inside colon
            $name = $attendance->employee->first_name;
            $message = 'You did not punch out yesterday';
            $regards ='HR';
            $subject = 'Missed Punch Out';
            $sendMailController->sendMail($to, $name, $subject, $message, $cc);
        }
        // dd($employees);
        return true;
    }

}

?>