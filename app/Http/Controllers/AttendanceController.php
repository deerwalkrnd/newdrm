<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Attendance;
use App\Models\MailControl;
use App\Models\LeaveRequest;
use App\Models\NoPunchInNoLeave;
use App\Http\Controllers\SendMailController;
use App\Helpers\MailHelper;
use App\Helpers\NepaliCalendarHelper;

use App\Models\LeaveType;
use App\Models\Time;
use Carbon\Carbon;
use App\Mail\LatePunchInMail;
Use Illuminate\Support\Facades\Mail;


class AttendanceController extends Controller
{
    private $redirect_to = '/dashboard';
    private $verificationCode = 'OXqSTexF5zn4uXSp';

    private function recordRowExists($employee_id)
    {
        // $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        $rowExists = Attendance::where('employee_id',$employee_id)
        ->whereDate('punch_in_time',$today)
        ->count();

        if($rowExists == 0)
            return false;
        else
            return true;
    }

    private function hasPunchOut()
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        $punchOutTime = Attendance::select('punch_out_time')
        ->where('employee_id',$employee_id)
        ->whereDate('punch_in_time',$today)
        ->first();

        if($punchOutTime->punch_out_time == null)
        {
            return false;
        }else{
            return true;
        }
    }

    public function index()
    {
        //state = 1 ; no punch-in
        //state = 2; no punch-out
        //state = 3; punch-out
        // dd(request()->ip());

        $state = 1;
        if($this->recordRowExists(\Auth::user()->employee_id))
        {
            $state = 2;
            if($this->hasPunchOut())
            {
                $state = 3;
            }
        }

        //set punch-in state;
        \Session::put('punchIn', $state);

        if($state == 1)
        {
            return view('admin.attendance.punchIn')->with(['code' => $this->verificationCode]);
        }else{
            return redirect($this->redirect_to);
        }
    }

    public function punchIn(Request $request)
    {
        $successfull_punch_in_res = [
                    'title' => 'Employee Punched In',
                    'message' => 'Employee has been successfully Punched In',
                    'icon' => 'success'
                    ];
        
        $failure_punch_in_res = [
                    'title' => 'Employee Punch In Failed',
                    'message' => 'Employee cannot be Punched In',
                    'icon' => 'warning'
                    ];

        //Employee punch in by HR
        if($request->id){
            $employee_id = $request->id;
            $reason = "HR Punch In";
            $attendance = $this->takeAttendance($employee_id,$request,$reason);
            if($attendance){
                $res = $successfull_punch_in_res;
            }else{
                $res = $failure_punch_in_res;
            }
            return redirect('/employee')->with(compact('res'));
        }else{
            //Individual Punch In
            $employee_id = \Auth::user()->employee_id;
            if($request->reason)
                $reason = $request->reason;
            else
                $reason = false;
            $attendance = $this->takeAttendance($employee_id,$request,$reason);
            if($attendance){
                $res = $successfull_punch_in_res;
            }else{
                $res = $failure_punch_in_res;
            }
            return redirect($this->redirect_to)->with(compact('res'));

        }   
    } 
    //force punch in for no-punch-in-no-leave request
    public function forcePunchIn(Request $request, $id)
    {
        //Employee punch in by HR
        $employee_id = $request->employee_id;
        $reason = "Forced Punch In";
        $attendance = $this->takeAttendance($employee_id,$request,$reason);
        if($attendance){
            $res = [
                'title' => 'Employee Punched In',
                'message' => 'Employee has been successfully Punched In',
                'icon' => 'success'
            ];
            $noPunchInNoLeaveRecord = NoPunchInNoLeave::findOrFail($id);
            $noPunchInNoLeaveRecord->delete();
        }else{
            $res = [
                'title' => 'Employee Punch In Failed',
                'message' => 'Employee cannot be Punched In',
                'icon' => 'warning'
            ];
        }
        return redirect()->back()->with(compact('res'));
    } 

    public function takeAttendance($employee_id, $request, $reason){
        if(!$this->recordRowExists($employee_id)){
            if($request->code == $this->verificationCode)
            {
                //no punch-in no leave
                if(strtolower($reason) == "forced punch in"){
                    $presentTime = Carbon::yesterday()->format('Y-m-d');
                    $punch_in_time =  Carbon::yesterday()->addHours(10);
                }
                else{
                    $presentTime = Carbon::now()->format('Y-m-d');
                    $punch_in_time = Carbon::now()->toDateTimeString();
                }

                // dd($presentTime,$punch_in_time);
                $hasAnyLeave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                            ->whereDate('end_date', '>=', $presentTime)
                            ->where('employee_id', $employee_id)->count();
                
                $maxTime = Time::select('id','time')->where('id','1')->first()->time;
                $first_half_leave_max_punch_in_time = Time::select('id','time')->where('id','2')->first()->time;
                
                if($hasAnyLeave == 0)
                {
                    $maxTime = strtotime(date('Y-m-d').' '.$maxTime);
                }else{
                    $leave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                            ->whereDate('end_date', '>=', $presentTime)
                            ->where('employee_id', $employee_id)->first();

                    $full_leave = $leave->full_leave;
                    if($full_leave == 0){
                        $half = $leave->half_leave;
                        if($half == 'first')
                        {
                            $maxTime = strtotime(date('Y-m-d').' '.$first_half_leave_max_punch_in_time);
                        }
                    }
                }

                $isLate = strtotime(Carbon::now()) <= strtotime($maxTime) ? '0' : '1';
                if($isLate)
                {
                    $request->validate([
                        'reason' => 'required|string|min:25',
                    ]); 
                }

                // if reason is null for isLate true throw error
                // dd(request()->ip());
                if(strtolower($reason) == "forced punch in"){
                    $attendance = Attendance::create([
                        'employee_id' => $employee_id,
                        'punch_in_time' => $punch_in_time,
                        'punch_out_time' =>  Carbon::yesterday()->addHours(18),
                        'punch_in_ip' => request()->ip(),
                        'punch_out_ip' => request()->ip(),
                        'late_punch_in' => $isLate,
                        'reason' => $reason
                    ]);
                    \Session::put('punchIn', '1');
                    // dd(|Session)

                }else{
                    $attendance = Attendance::create([
                        'employee_id' => $employee_id,
                        'punch_in_time' => $punch_in_time,
                        'punch_in_ip' => request()->ip(),
                        'late_punch_in' => $isLate,
                        'reason' => $reason
                    ]);
                    \Session::put('punchIn', '2');
                }
                //Send Mail to manager,hr and employee after late punch in 
                $subject = "Late Punch In";
                $send_mail = MailControl::select('send_mail')->where('name','Late Punch In')->first()->send_mail;
                if($attendance->late_punch_in && $send_mail){
                    Mail::to(\Auth::user()->employee->email)
                        ->cc(MailHelper::getManagerEmail($attendance->employee_id))
                        ->cc(MailHelper::getHrEmail())
                        ->send(new LatePunchInMail($attendance));
                    // MailHelper::sendEmail($type=2,$attendance,$subject);
                }
            }
            return true;
        }
        return false;
    }

    public function punchOut(Request $request)
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        if($this->recordRowExists($employee_id) && !$this->hasPunchOut())
        {
            // $presentTime = strtotime(Carbon::now());
            $presentTime = Carbon::now()->format('Y-m-d');

            $hasAnyLeave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                        ->whereDate('end_date', '>=', $presentTime)
                        ->where('employee_id', \Auth::user()->employee_id)->count();
            // dd($hasAnyLeave);
            $min_punch_out_time = Time::select('id','time')->where('id','3')->first()->time;
            $second_half_leave_min_punch_out_time = Time::select('id','time')->where('id','4')->first()->time;

            if($hasAnyLeave == 0)
            {
                $minTime = strtotime(date('Y-m-d').' '.$min_punch_out_time);
            }else{
                $leave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                        ->whereDate('end_date', '>=', $presentTime)
                        ->where('employee_id', \Auth::user()->employee_id)->first();
                $full_leave = $leave->full_leave;
                if($full_leave == 0){
                    $half = $leave->half_leave;
                    if($half == 'second')
                    {
                        // dd('her');
                        $minTime = strtotime(date('Y-m-d').' '.$second_half_leave_min_punch_out_time);
                        echo $minTime;
                    }else{
                        $minTime = strtotime(date('Y-m-d').' '.$min_punch_out_time);
                    }
                }
            }

            $issueForcedLeave = strtotime(Carbon::now()) < $minTime ? '1' : '0';
            $attendance = Attendance::select('punch_out_time')
                        ->where('employee_id',$employee_id)
                        ->whereDate('punch_in_time',$today)
                        ->update([
                            'punch_out_time' => Carbon::now()->toDateTimeString(),
                            'punch_out_ip' => request()->ip(),
                        ]);
            \Session::put('punchIn', '3');

            if($issueForcedLeave == 1)
            {
                try{
                LeaveRequest::create([
                    'employee_id' => \Auth::user()->employee_id,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                    'days' => '1',
                    'year' => $this->getNepaliYear(date('Y-m-d')),
                    'leave_type_id' => '1',
                    'full_leave' => '0',
                    'half_leave' => 'second',
                    'reason' => 'Forced (System)',
                    'acceptance' => 'accepted',
                    'requested_by' => \Auth::user()->employee_id,
                    'accepted_by' => NULL
                ]);
            }catch(Exception $e)
            {
                dd($e);
            }
                // dd("hereee2");
            }
        }

        return redirect($this->redirect_to);
    }

    public function myPunchIn()
    {
        $myPunchInList = Attendance::select('id','employee_id','punch_in_time','punch_in_ip','punch_out_time','punch_out_ip','missed_punch_out','late_punch_in')
                    ->where('employee_id',\Auth::user()->employee_id)
                    ->orderBy('punch_in_time','desc')
                    ->get();

        return view('admin.attendance.myPunchIn')->with(compact('myPunchInList'));
    }
    public function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            return $nepaliDateArray[0];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
}
