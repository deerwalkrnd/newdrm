<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Http\Controllers\SendMailController;
use App\Helpers\MailHelper;

use App\Models\LeaveType;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private $redirect_to = '/dashboard';
    private $verificationCode = 'OXqSTexF5zn4uXSp';

    private function recordRowExists()
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        $rowExists = Attendance::where('employee_id',$employee_id)
        ->whereDate('created_at',$today)
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
        ->whereDate('created_at',$today)
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
        if($this->recordRowExists())
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
        if(!$this->recordRowExists()){
            if($request->code == $this->verificationCode)
            {
                $presentTime = strtotime(Carbon::now());
                $hasAnyLeave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                            ->whereDate('end_date', '>=', $presentTime)
                            ->where('employee_id', \Auth::user()->employee_id)->count();

                if($hasAnyLeave == 0)
                {
                    $maxTime = strtotime(date('Y-m-d').' 09:20:00');
                }else{
                    $leave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                            ->whereDate('end_date', '>=', $presentTime)
                            ->where('employee_id', \Auth::user()->employee_id)->get();
                    $full_leave = $leave->full_leave;
                    if($full_leave == 0){
                        $half = $leave->half_leave;
                        if($half == 'second')
                        {
                            $maxTime = strtotime(date('Y-m-d').' 13:30:00');
                        }
                    }
                }
                
                $isLate = $presentTime <= $maxTime ? '0' : '1';
                $reason = $request->reason;

                if($isLate)
                {
                    $request->validate([
                        'reason' => 'required|string|min:25',
                    ]); 
                }


                // if reason is null for isLate true throw error
                // dd(request()->ip());
                $attendance = Attendance::create([
                    'employee_id' => \Auth::user()->employee_id,
                    'punch_in_time' => Carbon::now()->toDateTimeString(),
                    'punch_in_ip' => request()->ip(),
                    'late_punch_in' => $isLate,
                    'reason' => $reason
                ]);

                //Send Mail to manager,hr and employee after late punch in 
                $subject = "Late Punch In";
                if($attendance->late_punch_in){
                    MailHelper::sendEmail($type=2,$attendance,$subject);
                }
                \Session::put('punchIn', '2');
            }
        }

        return redirect($this->redirect_to);
    } 

    public function punchOut(Request $request)
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        if($this->recordRowExists() && !$this->hasPunchOut())
        {
            $presentTime = strtotime(Carbon::now());
            $hasAnyLeave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                        ->whereDate('end_date', '>=', $presentTime)
                        ->where('employee_id', \Auth::user()->employee_id)->count();

            if($hasAnyLeave == 0)
            {
                $minTime = strtotime(date('Y-m-d').' 18:00:00');
            }else{
                $leave = LeaveRequest::whereDate('start_date', '<=', $presentTime)
                        ->whereDate('end_date', '>=', $presentTime)
                        ->where('employee_id', \Auth::user()->employee_id)->get();
                $full_leave = $leave->full_leave;
                if($full_leave == 0){
                    $half = $leave->half_leave;
                    if($half == 'first')
                    {
                        $minTime = strtotime(date('Y-m-d').' 13:30:00');
                    }
                }
            }

            $issueForcedLeave = $presentTime < $minTime ? '1' : '0';

            $attendance = Attendance::select('punch_out_time')
                        ->where('employee_id',$employee_id)
                        ->whereDate('created_at',$today)
                        ->update([
                            'punch_out_time' => Carbon::now()->toDateTimeString(),
                            'punch_out_ip' => request()->ip(),
                        ]);
            \Session::put('punchIn', '3');

            $issueForcedLeave = 1;

            if($issueForcedLeave == 1)
            {
                // dd("Hereee");/
                try{
                LeaveRequest::create([
                    'employee_id' => \Auth::user()->employee_id,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                    'days' => '1',
                    'year' => date('Y'),
                    'leave_type_id' => '1',
                    'full_leave' => '0',
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
                    ->get();

        return view('admin.attendance.myPunchIn')->with(compact('myPunchInList'));
    }
}
