<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

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
                                        $employee->personalDaysTaken = $employee->halfLeave * 0.5 + $employee->fullLeave;
                                        $employee->sickDaysTaken    = $employee->sickHalfLeave * 0.5 + $employee->sickFullLeave;
                                        return $employee;
                                    })->toArray();  

            // unit and year wise max personal leave type                        
            $yearlyLeave = YearlyLeave::select('days')
                ->where('leave_type_id', 1)
                ->where(function($query) use ($unit) {
                    $query->where('unit_id', $unit)
                        ->orWhere('unit_id', null);
                })
                ->where('year', $year)
                ->first();

            $maxPersonalLeave = $yearlyLeave ? $yearlyLeave->days : 0;
            
            $sickLeave = YearlyLeave::select('days')
                                            ->where('leave_type_id',3)
                                            ->where(function($query) use ($unit){
                                                $query->where('unit_id',$unit)
                                                        ->orWhere('unit_id',null);
                                                })
                                            ->where('year',$year)
                                            ->first();
            $maxSickLeave = $sickLeave ? $sickLeave->days : 0;

            $employees = collect($employees)->map(function($record) use($maxPersonalLeave,$maxSickLeave,$year,$thisYear){
                
                $joinYearMonth = $this->getNepaliYear($record['join_date']);
                $joinYear = $joinYearMonth[0];
                $joinMonth = $joinYearMonth[1];

                $effectivePersonal = $maxPersonalLeave;
                $effectiveSick     = $maxSickLeave;

                if($joinYear < $thisYear && $joinYear == $year){
                    $remaining_month = 13 - $joinMonth;
                    $effectivePersonal = round(($effectivePersonal / 12 * $remaining_month) * 2) / 2;
                    $effectiveSick     = round(($effectiveSick     / 12 * $remaining_month) * 2) / 2;
                } elseif($joinYear == $thisYear){
                    $effectivePersonal = 0;
                    $effectiveSick     = 0;
                }

                $remainingPersonal = max($effectivePersonal - $record['personalDaysTaken'], 0);
                $remainingSick     = max($effectiveSick     - $record['sickDaysTaken'],     0);

                $record['employee_id']   = $record['id'];
                $record['personal_days'] = $remainingPersonal;
                $record['sick_days']     = $remainingSick;
                $record['days']          = $remainingPersonal + $remainingSick;
                $record['year']          = $year;
                unset($record['unit_id'],$record['join_date'],$record['fullLeave'],$record['halfLeave'],
                      $record['sickFullLeave'],$record['sickHalfLeave'],$record['id'],
                      $record['personalDaysTaken'],$record['sickDaysTaken']);
                return $record;
            })->toArray();
            
            $updateCarryOver = CarryOverLeave::upsert($employees, ['employee_id','year'], ['days','personal_days','sick_days']);
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
