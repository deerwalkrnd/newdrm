<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\YearlyLeave;
use App\Models\Unit;
use App\Models\Employee;
use App\Models\CarryOverLeave;
use App\Helpers\NepaliCalendarHelper;

class CarryOverLeaveController extends Controller
{
    // calculate the carry-over leave for every working employee
    // calculate by finding the max day between remaining personal leave and 8
    public function calculateCarryOverLeave()
    {
       //previous year
        try{
            $present_year = date('Y-m-d');
            $date = new NepaliCalendarHelper($present_year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            $year = $nepaliDateArray[0] - 1; //previous Year  
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
         
        //add the year column in leave request section
        $units = Unit::select('id')->get(); 
    
        foreach($units as $unit){
            $carryOverLeaveList = LeaveRequest::select('employee_id',\DB::raw('SUM(days) as days'))
                                        ->where('year',$year)
                                        ->where('acceptance','accepted')
                                        ->where('leave_type_id',1) //leave_type_id is seeded as 1 for personal leave
                                        ->whereHas('employee',function($query) use($unit){
                                            $query->where('unit_id',$unit->id);
                                        })
                                        ->groupBy('employee_id')
                                        ->get();

            //unit and year wise max personal leave type                        
            $maxPersonalLeave = YearlyLeave::select('days')
                                            ->where('leave_type_id',1)
                                            ->where(function($query) use ($unit){
                                                $query->where('unit_id',$unit)
                                                        ->orWhere('unit_id',null);
                                                })
                                            ->where('year',$year)
                                            ->first()->days;

            $carryOverLeaveList = collect($carryOverLeaveList)->map(function($record) use($maxPersonalLeave,$year){
                $remainingLeave = $maxPersonalLeave - $record['days'];
                $record['days'] = max(min($remainingLeave,8),0);
                $record['year'] = $year;
                return $record;
            })->toArray();

            CarryOverLeave::upsert($carryOverLeaveList,['employee_id','year'],['days']);
        }


        // return $carryOverLeaveList;
        return redirect('/dashboard');
    }
}
