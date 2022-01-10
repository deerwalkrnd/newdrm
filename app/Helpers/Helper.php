<?php
   
namespace App\Helpers;

use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\YearlyLeave;
use App\Models\CarryOverLeave;
use App\Models\Holiday;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

final class Helper
{
    private $weekend = ['SUN','SAT'];

    public static function getDays($start_date, $end_date, $leave_type_id){
        $employee = Employee::select('gender')->where('contract_status','active')->findOrFail(\Auth::user()->employee_id);
        $includeHoliday = LeaveType::select('include_holiday')->where('id',$leave_type_id)->get()->first();
        $s_date = date('Y-m-d',strtotime($start_date));
        $e_date = date('Y-m-d',strtotime($end_date));

        if($includeHoliday->include_holiday)
        {
            return (new self)->calculateTotalLeaveDays($s_date,$e_date);      
        }else{
            return (new self)->calculateLeaveDays($s_date,$e_date, $employee->gender);      
        }
    }

    /*
    * Calculate total leave days without any condition.
    * Return number of days between start_data and end_date
    */
    private function calculateTotalLeaveDays($start_date, $end_date)
    {
        $day = 0;
        do{
            $day++;
            $start_date = date('Y-m-d',strtotime($start_date.'+1 days'));          
        }while(strtotime($start_date) <= strtotime($end_date));
        
        return $day;
    }

    private function calculateLeaveDays($start_date, $end_date, $gender)
    {
        $holidayDates = Holiday::select('date')
                                ->where(function($query){
                                    $query->where('unit_id',\Auth::user()->employee->unit_id)
                                            ->orWhere('unit_id',null);
                                })
                                ->where('female_only','0')
                                ->whereYear('date',date('Y',strtotime($start_date)))
                                ->get()
                                ->toArray();

        $femaleHolidayDates = Holiday::select('date')
                                        ->where(function($query){
                                            $query->where('unit_id',\Auth::user()->employee->unit_id)
                                                    ->orWhere('unit_id',null);
                                        })
                                        ->whereYear('date',date('Y',strtotime($start_date)))
                                        ->where('female_only','1')                                    
                                        ->get()
                                        ->toArray();

        $holidayDates = array_column($holidayDates,'date');
        $femaleHolidayDates = array_column($femaleHolidayDates,'date');

        $day = 0;

        do{
            $weekDay = strtoupper(date('D',strtotime($start_date)));
            if(!(in_array($start_date,$holidayDates) || in_array($weekDay,$this->weekend))){
                if(!(strtolower($gender) == "female" && in_array($start_date,$femaleHolidayDates))){
                    $day++;
                }
            }

            $start_date = date('Y-m-d',strtotime($start_date.'+1 days'));          
        }while(strtotime($start_date) <= strtotime($end_date));

        return $day;
    }

    public static function getRemainingDays($leave_type_id){
        $year = date('Y');
        $already_taken_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->sum('days');

        $allowed_leave = YearlyLeave::select('days')
                                        ->where('unit_id', \Auth::user()->employee->unit_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->where('year',$year)
                                        ->first();

        if(!$allowed_leave)
        {
            $allowed_leave = YearlyLeave::select('days')
                                        ->where('unit_id', null)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->where('year',$year)
                                        ->first();
        }

        if($allowed_leave)
            $allowed_leave = $allowed_leave->days;
        else
            $allowed_leave = 0;

        $remaining_leave = $allowed_leave - $already_taken_leaves;
       
        return $remaining_leave;     
    }

    public static function getRemainingCarryOverLeave()
    {
        $year = date('Y');
        $already_taken_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', \Auth::user()->employee_id)
                                        ->where('leave_type_id',2) // 2 for carry_over leave
                                        ->sum('days');

        $allowed_leave = CarryOverLeave::select('days')
                                        ->where('year',date('Y')-1)
                                        ->where('employee_id',\Auth::user()->employee_id)
                                        ->first();

        if($allowed_leave)
            $allowed_leave = $allowed_leave->days;
        else
            $allowed_leave = 0;
                               
        $remaining_leave = $allowed_leave - $already_taken_leaves;
       
        return $remaining_leave;     
    }
}
?>