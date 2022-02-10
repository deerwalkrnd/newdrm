<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CarryOverLeave;
use App\Helpers\NepaliCalendarHelper;
use App\Models\Time;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(Request $request)
    {       
        $state = 1;
        if($this->recordRowExists())
        {
            $state = 2;
            if($this->hasPunchOut())
            {
                $state = 3;
            }
        }

        $maxTime = Time::select('id','time')->where('id','1')->first()->time;
        $isLate = strtotime(Carbon::now()) <= $maxTime ? '0' : '1';

        //set punch-in state;
        \Session::put('punchIn', $state);
        \Session::put('userIp', request()->ip());
        \Session::put('isLate', $isLate);

        $leaveBalance = $this->getLeaveBalance();
        $birthdayList = $this->getBirthdayList();
        $leaveList = $this->getLeaveList();

        if(\Auth::user()->password_expired != '0')
        {
            return redirect('/change-password');
        }else{
            return view('admin.dashboard.index')->with(compact('leaveBalance','birthdayList','leaveList'));
        }
        
    }

    private function getLeaveList()
    {
        $leaveList = LeaveRequest::select('id','employee_id','start_date','end_date','days','full_leave','half_leave','leave_type_id')
                        ->with('employee:id,first_name,last_name,middle_name')
                        ->with('leaveType:id,name')
                        ->where('acceptance','accepted')
                        ->whereDate('start_date','<=',date('Y-m-d'))
                        ->whereDate('end_date','>=',date('Y-m-d'))
                        ->get();

        return $leaveList;
    }

    private function getBirthdayList()
    {
        $curr_month = date('m');
        $curr_day = date('d');

        $next_month = $curr_month + 1;

        $birthdayList = Employee::select('first_name','last_name','middle_name','date_of_birth')
                                ->where('contract_status','active')
                                ->where(function($query) use($curr_month,$curr_day){
                                    $query->whereMonth('date_of_birth','>',$curr_month)
                                            ->orWhere(function($query) use($curr_month,$curr_day){
                                                $query->whereMonth('date_of_birth',$curr_month)
                                                    ->whereDay('date_of_birth','>',$curr_day);
                                            });   
                                })
                                ->where(function($query) use($next_month){
                                    $query->whereMonth('date_of_birth','<=',$next_month); 
                                })
                                ->orderByRaw("DATE_FORMAT(date_of_birth,'%M-%d-%Y')")
                                ->get();

        return $birthdayList;
    }

    private function getLeaveBalance()
    {
        try{
            $date = new NepaliCalendarHelper(date('Y-m-d'),1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            $year = $nepaliDateArray[0];
            $month = $nepaliDateArray[1];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
        // dd($year,$month);

        $unit_id = \Auth::user()->employee->unit_id;
        $leaveTypes = LeaveType::select('name','id')->where('gender','all')->orWhere('gender',ucfirst(\Auth::user()->employee->gender))->get();

        $lists = array();
        foreach($leaveTypes as $leaveType)
        {
            $allowedLeave = $this->getAllowedLeaveDays($unit_id,$leaveType->id,$year);
            $acquiredLeave = $allowedLeave / 12 * $month;
            
            $fullLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave','year')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',$leaveType->id)
                                        ->where('full_leave',"1")
                                        ->sum('days');

            $halfLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',$leaveType->id)
                                        ->where('full_leave',"0")
                                        ->sum('days');

            $leaveTaken = $fullLeaveTaken + 0.5 * $halfLeaveTaken;
            $balance = $acquiredLeave - $leaveTaken;

            $lists[$leaveType->name] = [
                'allowed' => $allowedLeave,
                'accrued' => round($acquiredLeave,2),
                'taken' => $leaveTaken,
                'balance' => round($balance,2)
            ];
        }

        return $lists;
    }

    public function getAllowedLeaveDays($unit_id,$leaveType,$year)
    {
        // if carry Over Leave // carry over is set to 1
        if($leaveType == 2)
        {
            $allowedLeave = CarryOverLeave::select('id','year','days','employee_id')
                                            ->where('year',$year-1)
                                            ->where('employee_id',\Auth::user()->employee_id)
                                            ->first();
        }else{
            $allowedLeave = YearlyLeave::select('days')
                                        ->where('year',$year)
                                        ->where('unit_id',$unit_id)
                                        ->where('leave_type_id',$leaveType)
                                        ->where('status','active')
                                        ->first();

            if(!$allowedLeave)
            {
                $allowedLeave = YearlyLeave::select('days')
                ->where('year',$year)
                ->where('unit_id',null)
                ->where('leave_type_id',$leaveType)
                ->where('status','active')->first();
            }
        }
        
        if(isset($allowedLeave) && ($allowedLeave->exists() == 1))
            $allowedLeave = $allowedLeave->days;
        else
            $allowedLeave = 0;

        return $allowedLeave;
    }

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
}
