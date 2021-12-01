<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private $redirect_to = '/organization';
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

        // dd(session('punchIn'));

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
                $maxTime = strtotime(date('Y-m-d').' 09:20:00');
                $presentTime = strtotime(Carbon::now());
                $isLate = $presentTime <= $maxTime ? '0' : '1';
                $reason = $request->reason;
                // if reason is null for isLate true throw error
                Attendance::create([
                    'employee_id' => \Auth::user()->employee_id,
                    'punch_in_time' => Carbon::now()->toDateTimeString(),
                    'late_punch_in' => $isLate,
                    'reason' => $reason
                ]);
                \Session::put('punchIn', '2');
            }
        }

        return redirect($this->redirect_to);
    } 

    public function punchOut()
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');
        if($this->recordRowExists() && !$this->hasPunchOut())
        {
            $attendance = Attendance::select('punch_out_time')
                        ->where('employee_id',$employee_id)
                        ->whereDate('created_at',$today)
                        ->update([
                            'punch_out_time' => Carbon::now()->toDateTimeString()
                        ]);
            \Session::put('punchIn', '3');
        }

        return redirect($this->redirect_to);
    }
}
