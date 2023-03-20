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
        $thisYearMonth = $this->getNepaliYear(date('Y-m-d'));
        $thisYear = $thisYearMonth[0];
        $year = $thisYear-1; //previous year

        //add the year column in leave request section
        $units = Unit::select('id')->get(); 

        foreach($units as $unit){           
            $employees = Employee::select('id','unit_id','join_date')->where('contract_status','active')
                                    ->where('unit_id',$unit->id)
                                    ->withSum(['leaveRequest as halfLeave' => function($query) use($year){
                                        $query->where('year',$year)
                                        ->where('acceptance','accepted')
                                        ->where('leave_type_id','1')
                                        ->where('full_leave','0');
                                    }],'days')
                                    ->withSum(['leaveRequest as fullLeave' => function($query) use($year){
                                        $query->where('year',$year)
                                        ->where('acceptance','accepted')
                                        ->where('leave_type_id','1')
                                        ->where('full_leave','1');
                                    }],'days')
                                    ->withSum(['leaveRequest as sickHalfLeave' => function($query) use($year){
                                        $query->where('year',$year)
                                        ->where('acceptance','accepted')
                                        ->where('leave_type_id','3')
                                        ->where('full_leave','0');
                                    }],'days')
                                    ->withSum(['leaveRequest as sickFullLeave' => function($query) use($year){
                                        $query->where('year',$year)
                                        ->where('acceptance','accepted')
                                        ->where('leave_type_id','3')
                                        ->where('full_leave','1');
                                    }],'days')
                                    ->get()
                                    ->map(function($employee){
                                        $employee->days = $employee->halfLeave * 0.5 + $employee->fullLeave + $employee->sickHalfLeave * 0.5 + $employee->sickFullLeave;
                                        return $employee;
                                    })->toArray();  

            // unit and year wise max personal leave type                        
            $maxPersonalLeave = YearlyLeave::select('days')
                                            ->where('leave_type_id',1)
                                            ->where(function($query) use ($unit){
                                                $query->where('unit_id',$unit)
                                                        ->orWhere('unit_id',null);
                                                })
                                            ->where('year',$year)
                                            ->first()->days;
            
            $maxSickLeave = YearlyLeave::select('days')
                                            ->where('leave_type_id',3)
                                            ->where(function($query) use ($unit){
                                                $query->where('unit_id',$unit)
                                                        ->orWhere('unit_id',null);
                                                })
                                            ->where('year',$year)
                                            ->first()->days; 

            $maxMixLeave = $maxPersonalLeave + $maxSickLeave;

            $employees = collect($employees)->map(function($record) use($maxMixLeave,$year,$thisYear){
                
                $joinYearMonth = $this->getNepaliYear($record['join_date']);
                $joinYear = $joinYearMonth[0];
                $joinMonth = $joinYearMonth[1];
                
                //if joinyear is this year or greater than this year, leave allowance is calculated from joined month
                if($joinYear < $thisYear && $joinYear == $year){
                    $remaining_month = 13-$joinMonth;
                    $maxMixLeave = round(($maxMixLeave/12*$remaining_month)*2)/2; 
                }elseif($joinYear == $thisYear){    //if joinyear is this year, carry over leave should be 0
                    $maxMixLeave = 0;
                }
                 
                // if join date is in previous year calculate myPersonalLeave+mySickLeave else mypersonalLeave = $maxPersonalLeave+maxSickLeave
                $remainingLeave = $maxMixLeave - $record['days'];
              
                $record['employee_id'] = $record['id'];
                $record['days'] = max(min($remainingLeave,8),0);
                $record['year'] = $year;
                unset($record['unit_id'],$record['join_date'],$record['fullLeave'],$record['halfLeave'],$record['sickFullLeave'],$record['sickHalfLeave'],$record['id']);
                return $record;
            })->toArray();
            
            $updateCarryOver = CarryOverLeave::upsert($employees,['employee_id','year'],['days']);
        }
        if($updateCarryOver)
            return response()->json([
                'success' => true,
                'message' => "Carry Over Leave has been Created Successfully.",
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => "Error Occured while creating Carry-Over Leave.",
            ]);
        // return redirect('/dashboard');
    }

    //calculate leave days ofeach employee usign both half and full leave
    private function calculateDays($carryOverLeaveList){
        $days = 0;
        if($carryOverLeaveList){
            foreach($carryOverLeaveList as $leaveRequestList){
                if($leaveRequestList->full_leave == 1){
                    $days = $days + $leaveRequestList->days; 
                }else{
                    $days = $days + $leaveRequestList->days * 0.5; 
                }
            }
        }
        // dd($days);
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
