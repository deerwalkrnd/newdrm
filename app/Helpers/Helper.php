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

    public static function getDays($start_date, $end_date, $leave_type_id,$employee_id){
        $employee = Employee::select('id','gender','unit_id','join_date')->where('contract_status','active')->findOrFail($employee_id);
        $includeHoliday = LeaveType::select('include_holiday')->where('id',$leave_type_id)->get()->first();
        $s_date = date('Y-m-d',strtotime($start_date));
        $e_date = date('Y-m-d',strtotime($end_date));
        if($includeHoliday->include_holiday)
        {
            return (new self)->calculateTotalLeaveDays($s_date,$e_date);      
        }else{
            return (new self)->calculateLeaveDays($s_date,$e_date, $employee);      
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

    private function calculateLeaveDays($start_date, $end_date, $employee)
    {
        $unit_id = $employee->unit_id;
        $holidayDates = Holiday::select('date')
                                ->where(function($query) use ($unit_id){
                                    $query->where('unit_id',$unit_id)
                                            ->orWhere('unit_id',null);
                                })
                                ->where('female_only','0')
                                ->whereYear('date',date('Y',strtotime($start_date)))
                                ->get()
                                ->toArray();
        
        $femaleHolidayDates = Holiday::select('date')
                                        ->where(function($query) use ($unit_id){
                                            $query->where('unit_id',$unit_id)
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
                if(!(strtolower($employee->gender) == "female" && in_array($start_date,$femaleHolidayDates))){
                    $day++;
                }
            }

            $start_date = date('Y-m-d',strtotime($start_date.'+1 days'));          
        }while(strtotime($start_date) <= strtotime($end_date));

        return $day;
    }

    // returns the total remaining days of particular leave type of an employee
    public static function getRemainingDays($leave_type_id,$employee){
        $year = (new self)->getNepaliYear(date('Y-m-d'))[0];
        
        $employee_join_year_month = (new self)->getNepaliYear($employee->join_date);
        $employee_join_year = $employee_join_year_month[0];
        $employee_join_month = $employee_join_year_month[1];

        // dd($year,$employee_join_year,$employee_join_month,'remaining days');  
        $allowed_leave = YearlyLeave::select('days')
                                        ->where('unit_id', $employee->unit_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->where('year',$year)
                                        ->first();

        
        // dd('allowed leaves',$allowed_leave.'remaining_month',$remaining_month,'join month',$employee_join_month);
        
        //null unit id
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

        if($employee_join_year >= $year){
            $remaining_month = 13-$employee_join_month;
            $allowed_leave = round(($allowed_leave/12*$remaining_month)*2)/2;
        }

        $already_taken_leaves = (new self)->getAlreadyTakenLeaves($year,$leave_type_id,$employee->id);

        // $already_taken_total_leaves = $already_taken_full_leaves + $already_taken_half_leaves/2;
        $remaining_leave = $allowed_leave - $already_taken_leaves;
        // dd('allowed leave',$allowed_leave,$already_taken_total_leaves,$remaining_leave);

        return $remaining_leave;     
    }

    public static function getRemainingCarryOverLeave($employee)
    {
        $year = (new self)->getNepaliYear(date('Y-m-d'))[0];
        if((new self)->getNepaliYear($employee->join_date ) == $year)   //if joined thhis year then carry over = 0
            $remaining_leave = 0;
        else{
            $already_taken_leaves = (new self)->getAlreadyTakenLeaves($year,2,$employee->id);
            $allowed_leave = CarryOverLeave::select('days')
                                            ->where('year',$year-1)
                                            ->where('employee_id',$employee->id)
                                            ->first();
            if($allowed_leave)
                $allowed_leave = $allowed_leave->days;
            else
                $allowed_leave = 0;
            
            $remaining_leave = $allowed_leave - $already_taken_leaves;
            }
            return $remaining_leave;     
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

    private function getAlreadyTakenLeaves($year,$leave_type_id,$employee_id){
        $already_taken_full_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('full_leave','1')
                                        ->where('employee_id', $employee_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->sum('days');
        $already_taken_half_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('half_leave','!=','null')
                                        ->where('employee_id', $employee_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->sum('days');
        $already_taken_total_leaves = $already_taken_full_leaves + $already_taken_half_leaves/2;
        return $already_taken_total_leaves;
    }
}
?>