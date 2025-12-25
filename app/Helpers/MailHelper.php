<?php

namespace App\Helpers;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingLeaveNotificationMail;
use App\Mail\TestMail;
use App\Mail\SendMail;
use App\Mail\LeaveMail;
use App\Mail\MissedPunchOutMail;
use Illuminate\Support\Facades\DB;

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
        // $hr = User::whereHas('role',function($q){
        //     $q->where('authority','hr');
        // })
        // ->whereHas('employee',function($q){
        //     $q->where('contract_status','active');
        // })
        // ->with('employee:id,email')->get()->map(function($arr){
        //     return $arr->employee->email;
        // })->toArray();

        $hr = ['hitesh.karki@deerwalk.edu.np','aakancha.thapa@deerwalk.edu.np','alisha.thapa@deerwalk.edu.np','ujjwal.poudel@sifal.deerwalk.edu.np','pooja.neupane@deerwalk.edu.np'];

        return $hr;
    }

    //Missed Punch Out Mail
    public static function sendMissedPunchOutMail(){
        $attendances = Attendance::where('missed_punch_out','1')
                                ->with('employee:id,first_name,middle_name,last_name,manager_id,email')
                                ->whereDate('punch_in_time',date('Y-m-d'))        //for cron job
                                ->get();

        foreach($attendances as $attendance){
            try{
                $employee_name = $attendance->employee->first_name.' '.$attendance->employee->middle_name.' '.$attendance->employee->last_name;
                $ccList =  (new self)->getHrEmail();
                array_push($ccList,(new self)->getManagerEmail($attendance->employee_id));

                $mail= Mail::to($attendance->employee->email)
                    ->cc($ccList)
                    ->queue(new MissedPunchOutMail($employee_name));
            }catch(\Exception $e){
                \Log::debug($e);
                redirect()->back()->with('error',$e->getMessage());
            }
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
        foreach($leaveRequests as $leaveRequest){
            $to = $leaveRequest->employee->email;
            $name = $leaveRequest->employee->first_name;
            $ccList =  (new self)->getHrEmail();
            array_push($ccList,(new self)->getManagerEmail($leaveRequest->employee_id));
            $mail = Mail::to($to)
                ->cc($ccList)
                ->queue(new PendingLeaveNotificationMail($name));
        }
        return true;
    }

    public static function morningLeaveMail(){
        $leaveList = LeaveRequest::select('id','employee_id','start_date','end_date','days','full_leave','half_leave','leave_type_id')
                        ->with('employee:id,first_name,last_name,middle_name,unit_id')
                        ->with('leaveType:id,name')
                        ->where('acceptance','accepted')
                        ->whereDate('start_date','<=',date('Y-m-d'))
                        ->whereDate('end_date','>=',date('Y-m-d'))
                        ->get();

        $deerwalk_sifal = Leaverequest::whereDate('start_date','<=',date('Y-m-d'))
                    ->whereDate('end_date','>=',date('Y-m-d'))
                    ->where('acceptance','accepted')
                    ->whereHas('employee',function($query){
                        $query->where('unit_id',11);
                    })
                    ->get()
                    ->count();

        $deerwalk_compware = Leaverequest::whereDate('start_date','<=',date('Y-m-d'))
                    ->whereDate('end_date','>=',date('Y-m-d'))
                    ->where('acceptance','accepted')
                    ->whereHas('employee',function($query){
                        $query->where('unit_id',10);
                    })
                    ->get()
                    ->count();

        $deerwalk_group = Leaverequest::whereDate('start_date','<=',date('Y-m-d'))
                    ->whereDate('end_date','>=',date('Y-m-d'))
                    ->where('acceptance','accepted')
                    ->whereHas('employee',function($query){
                        $query->where('unit_id',1)
                                ->orWhere('unit_id',13);
                    })
                    ->get()
                    ->count();

        $mail= Mail::to(explode(',',env('GP_EMAIL')))
                ->queue(new LeaveMail($leaveList,$deerwalk_compware,$deerwalk_group,$deerwalk_sifal));

        return true;
    }

    // cron job running
    public static function testMail2(){
        $name = "test";
        Mail::to('bijay.shrestha@deerwalk.edu.np')
                ->cc(['sunav.sharma@deerwalk.edu.np'])
                ->queue(new SendMail($name));
        return true;
    }

    public static function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            \Log::debug($nepaliDateArray);
            return $nepaliDateArray[0];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }


}

?>
