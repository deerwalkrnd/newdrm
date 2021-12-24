<?php
   
namespace App\Helpers;

use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

final class Helper
{
    private $weekend = ['SUN','SAT'];
    

    public static function getDays($start_date, $end_date, $leave_type_id){
        $employee = Employee::findOrFail(\Auth::user()->id)->select('gender')->first();
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
        $holidayDates = Holiday::select('date')->where('female_only',0)->get()->toArray();
        $femaleHolidayDates = Holiday::select('date')->where('female_only',1)->get()->toArray();
        $holidayDates = array_column($holidayDates,'date');

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

}
?>