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
use App\Helpers\MailHelper;
use App\Models\NoPunchInNoLeave;
use App\Models\Time;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->isPasswordExpired();
       
        $leaveBalance = $this->getLeaveBalance();
        $birthdayList = $this->getBirthdayList();
        $leaveList = $this->getLeaveList();

        $state = $this->getAttendanceState();
        $userIp  = request()->ip();

        $late_within_ten_days = $this->isLateWithinTenDays();
        $max_punch_in_time = $this->getMaxPunchInTime();
        $noPunchInNoLeaveRecordExists = NoPunchInNoLeave::where('employee_id',\Auth::user()->employee_id)->get()->count() > 0;

        if($this->hasPendingLeaveRequest())
            $this->setManagerNotification();

            // dd(compact(
            //     'leaveBalance',
            //     'birthdayList',
            //     'leaveList',
            //     'state',
            //     'userIp',
            //     'late_within_ten_days',
            //     'max_punch_in_time',
            //     'noPunchInNoLeaveRecordExists'
            // ));

        return view('admin.dashboard.index')
                ->with(compact(
                    'leaveBalance',
                    'birthdayList',
                    'leaveList',
                    'state',
                    'userIp',
                    'late_within_ten_days',
                    'max_punch_in_time',
                    'noPunchInNoLeaveRecordExists'
                ));      
    }

    private function getAttendanceState()
    {
        // set state = 1 for no punch in
        // set state = 2 for no punch out
        // set state = 3 for punch out

        $state = 1;
        if($this->recordRowExists())
        {
            $state = 2;
            if($this->hasPunchOut())
            {
                $state = 3;
            }
        }

        return $state;
    }

    private function isPasswordExpired()
    {
        if(\Auth::check() && \Auth::user()->password_expired != '0')
            return redirect('/change-password');
    }

    private function isEmployeeOnLeave()
    {
        $leaveRequest = LeaveRequest::where('employee_id', \Auth::user()->employee_id)
                    ->whereDate('start_date', '<=', date('Y-m-d'))
                    ->whereDate('end_date', '>=', date('Y-m-d'))
                    ->where('acceptance','accepted');

        if($leaveRequest->count() > 0)
        {
            $leave = $leaveRequest->first();

            if($leave->full_leave == 1)
                return "full";
            elseif($leave->half_leave == "first")
                return "first";
            elseif($leave->half_leave == "second")
                return "second";
            else
                return false;
        }
    
        return  false;     
    }

    private function getEmployeeShift()
    {
        return strtolower(\Auth::user()->employee->shift->name);        
    }

    private function isHoliday()
    {
        if(strtolower(date('D')) == 'sat' || strtolower(date('D')) == 'sun')
        {
            return true;            
        }else{
            return false;   
        }
    }

    private function getMaxPunchInTime()
    {
        // if employee is on any holiday - no time is required
        if($this->isHoliday() || $this->isEmployeeOnLeave() == "full")
        {
            return false;
        }else{
            // if employee is not on holiday max time is set max time
            $maxTime = Time::select('time')->where('name',Time::Max_Punch_In_Time)->first()->time;
 
            // if employee has custom shift punch in time is set accordingly
            if($this->getEmployeeShift() == "custom")
            {
                $maxTime = \Auth::user()->employee->start_time;
            }

            // if employee is on first half leave max time is set to first half max time
            if($this->isEmployeeOnLeave())
            {
                if($this->isEmployeeOnLeave() == "first")
                {
                    $maxTime = Time::select('time')->where('name',Time::First_Half_Punch_In_Time)->first()->time;
                }
            }
        }

        return $maxTime;
    }

    private function isLateWithinTenDays()
    {
        $late_within_ten_days = Attendance::select('late_punch_in','punch_in_time')
            ->where('employee_id', \Auth::user()->employee_id)
            ->whereDate('punch_in_time','>=',date('Y-m-d',strtotime("-10 days")))
            ->where('late_punch_in','1')
            ->count();

        if($late_within_ten_days > 0)
            return true;
        else
            return false;
    }

    private function hasPendingLeaveRequest()
    {
        if(\Auth::user()->role->authority != 'manager'){
            return false;
        }else{
            $count = LeaveRequest::whereHas('employee',function($query){
                $query->where('manager_id',\Auth::user()->employee_id);
            })->where('acceptance','pending')->count();
            
            if($count > 0)
                return true;
            else
                return false;
        }
    }

    private function setManagerNotification()
    {
        $res = [
            'title' => 'Leave Acceptance Pending',
            'message' => 'You have pending leave request of employees. Please perform the required action.',
            'icon' => 'warning'
        ];
        
        session(['res'=>$res]);
    }


    // public function index(Request $request)
    // {       
    //     $state = 1;
    //     if($this->recordRowExists())
    //     {
    //         $state = 2;
    //         if($this->hasPunchOut())
    //         {
    //             $state = 3;
    //         }
    //     }

    //     //Custom shift Employee
    //     $employee_shift_time = Employee::select('id','shift_id','start_time','end_time')->where('id',\Auth::user()->employee_id)->first(); 
    //     $isWeekend = 0;
        
    //     if(date('D') == 'Sat' || date('D') == 'Sun'){    //weekend punchin
    //         $maxTime = date('H:i:s',strtotime('+60seconds'));       //works by isLate but just to understand
    //         $isWeekend = 1;                                      //isWeekend is introduced
    //     }
    //     else if($employee_shift_time->shift_id == '2')   //shift_id = 2 ->custom shift
    //         $maxTime = $employee_shift_time->start_time;    //maxPunch in time for custom shift employees
    //     else
    //         $maxTime = Time::select('id','time')->where('id','1')->first()->time;    //max punch in time for other shifts

    //     //check if late
    //     $first_half_leave_max_punch_in_time = Time::select('id','time')->where('id','2')->first()->time;
    //     $hasAnyLeave = LeaveRequest::whereDate('start_date', '<=', date('Y-m-d'))
    //                     ->whereDate('end_date', '>=', date('Y-m-d'))
    //                     ->where('employee_id', \Auth::user()->employee_id)
    //                     ->where('acceptance','accepted')
    //                     ->count();
    //     if($hasAnyLeave == 0)
    //     {
    //         $maxTime = strtotime(date('Y-m-d').' '.$maxTime);
    //     }else{
    //         $leave = LeaveRequest::whereDate('start_date', '<=', date('Y-m-d'))
    //                 ->whereDate('end_date', '>=', date('Y-m-d'))
    //                 ->where('employee_id', \Auth::user()->employee_id)
    //                 ->where('acceptance','accepted')
    //                 ->first();

    //         $full_leave = $leave->full_leave;
    //         if($full_leave == 0){
    //             $half = $leave->half_leave;
    //             if($half == 'first')
    //             {
    //                 $maxTime = strtotime(date('Y-m-d').' '.$first_half_leave_max_punch_in_time);
    //             }
    //         }
    //     }

    //     $isLate = time() <= $maxTime ? '0' : '1';
    //     // dd($isLate);
    //     $late_within_ten_days = Attendance::select('late_punch_in','punch_in_time')
    //         ->where('employee_id', \Auth::user()->employee_id)
    //         ->whereDate('punch_in_time','>=',date('Y-m-d',strtotime("-10 days")))
    //         ->where('late_punch_in','1')
    //         ->count();

    //     //check if there exists no punch in no leave record
    //     $noPunchInNoLeaveRecords = NoPunchInNoLeave::where('employee_id',\Auth::user()->employee_id)->get()->count();

    //     //set punch-in state;
    //     \Session::put('punchIn', $state);
    //     \Session::put('userIp', request()->ip());
    //     \Session::put('isLate', $isLate);
    //     \Session::put('isWeekend', $isWeekend);
    //     \Session::put('late_within_ten_days',$late_within_ten_days);
    //     \Session::put('noPunchInNoLeaveRecords',$noPunchInNoLeaveRecords);

    //     $leaveBalance = $this->getLeaveBalance();
    //     $birthdayList = $this->getBirthdayList();
    //     $leaveList = $this->getLeaveList();
    //     $res=[];
    //     if(\Auth::user()->role->authority == 'manager'){
    //         $employees_under_manager = Employee::select('id')->where('manager_id',\Auth::user()->employee_id)->get();
    //         foreach($employees_under_manager as $employee){
    //             if(LeaveRequest::where('employee_id',$employee->id)->where('acceptance','pending')->first()){
    //                 $res = [
    //                     'title' => 'Leave Acceptance Pending',
    //                     'message' => 'You have pending leave request of employees. Please perform the required action.',
    //                     'icon' => 'warning'
    //                 ];
    //                 session(['res'=>$res]);
    //             }
    //         }
    //     }
    //     if(\Auth::user()->password_expired != '0')
    //     {
    //         return redirect('/change-password');
    //     }else{
    //         return view('admin.dashboard.index')->with(compact('leaveBalance','birthdayList','leaveList'));
    //     }
    // }

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
                                ->orderByRaw("DATE_FORMAT(date_of_birth,'%m-%d-%Y')")
                                ->get();

        return $birthdayList;
    }

    private function getLeaveBalance()
    {
        $currentYearMonth = $this->getNepaliYear(date('Y-m-d'));
        $year = $currentYearMonth[0];
        $month = $currentYearMonth[1];

        $unit_id = \Auth::user()->employee->unit_id;
        $leaveTypes = LeaveType::select('name','id')
                                ->where('status','active')
                                ->where(function($query){
                                    $query->where('gender','all')
                                    ->orWhere('gender',ucfirst(\Auth::user()->employee->gender));
                                })
                                ->get();
                                
        $lists = array();
        foreach($leaveTypes as $leaveType)
        {
            // get employee join date
            // if year < this year no change
            // else months remaining in year i.e 13 - join month
            // allowe leave = allow / 12 * remaining months
            $join_date = Employee::select('id','join_date')->where('id',\Auth::user()->employee_id)->first()->join_date;
            $joinYearMonth = $this->getNepaliYear($join_date);
            $joinYear = $joinYearMonth[0];
            $joinMonth = $joinYearMonth[1];

            $allowedLeave = $this->getAllowedLeaveDays($unit_id,$leaveType->id,$year,\Auth::user()->employee_id);
            //if joinyear is this year or greater than this year, leave allowance is calculated from joined month
            $remaining_month = 0;
            $acquiredMonth = 0;

            // dd($allowedLeave);

            if($joinYear >= $year){
                $remaining_month = 13-$joinMonth;
                $allowedLeave = round(($allowedLeave/12*$remaining_month)*2)/2;
            }

            if($joinYear == $year){
                $acquiredMonth = $month - $joinMonth + 1;      
            }

            
           
            $fullLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave','year')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',$leaveType->id)
                                        ->where('full_leave',"1")
                                        ->sum('days');

            // dd($fullLeaveTaken);

            $halfLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',$leaveType->id)
                                        ->where('full_leave',"0")
                                        ->sum('days');

            $leaveTaken = $fullLeaveTaken + 0.5 * $halfLeaveTaken;
            $acquiredLeave = $allowedLeave;

            if($leaveType->id != '2' && $leaveType->id != '13' && $leaveType->id != '6' && $leaveType->id != '10'){
                $acquiredLeave = round(($allowedLeave / 12 * $month) * 2) / 2;
                
                if($joinYear == $year){
                    $acquiredLeave = round($allowedLeave / 12 * $acquiredMonth * 2) / 2;
                }
            }
        
            $balance = $acquiredLeave - $leaveTaken;

            $lists[$leaveType->name] = [
                'allowed' => $allowedLeave,
                'accrued' => $acquiredLeave,
                'taken' => $leaveTaken,
                'balance' => $balance,
            ];
        }

        return $lists;
    }

    public function getAllowedLeaveDays($unit_id,$leaveType,$year,$employee_id)
    {
        // if carry Over Leave // carry over is set to 2
        if($leaveType == 2)
        {
            $employee_id = strval($employee_id);
            $allowedLeave = CarryOverLeave::where('employee_id',$employee_id)
                                            ->where('year',$year-1)
                                            // ->get();
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
    
    private function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            $year_month = [$nepaliDateArray[0],$nepaliDateArray[1]];
            return $year_month;
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
}
