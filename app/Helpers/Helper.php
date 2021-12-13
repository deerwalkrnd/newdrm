<?php
   
namespace App\Helpers;

use App\Models\Holiday;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

final class Helper
{
    public static function getDays($start_date, $end_date){
    
        $holidayDates = Holiday::select('date')->where('female_only',0)->get()->toArray();
        $femaleHolidayDates = Holiday::select('date')->where('female_only',1)->get()->toArray();
        $employee = Employee::findOrFail(\Auth::user()->id)->select('gender')->first();
        $holidayDates = array_column($holidayDates,'date');

        $s_date = date('Y-m-d',strtotime($start_date));
        $e_date = date('Y-m-d',strtotime($end_date));

        $t_date = $s_date;
        $weekend = ['SUN','SAT'];
        $day = 0;
        do{
            $t_date = date('Y-m-d',strtotime($t_date.'+1 days'));          
            $weekDay = strtoupper(date('D',strtotime($t_date)));

            if(!(in_array($t_date,$holidayDates) || in_array($weekDay,$weekend))){
                if(!(strtolower($employee->gender) == "female" && in_array($t_date,$femaleHolidayDates))){
                    $day++;
                }
            }
        }while(strtotime($t_date) <= strtotime($e_date));

        // dd($day);
        return $day;
    }
}
?>