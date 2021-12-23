<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\CarryOverLeave;


class CarryOverLeaveController extends Controller
{
    // calculate the carry-over leave for every working employee
    // calculate by finding the max day between remaining personal leave and 8
    public function calculateCarryOverLeave()
    {
        $year = date('Y') - 1; //previous year
        //add the year column in leave request section
        $maxPersonalLeave = 13;
        $carryOverLeaveList = LeaveRequest::select('employee_id',\DB::raw('SUM(days) as days'))
                                            ->where('year',$year)
                                            ->where('acceptance','accepted')
                                            ->where('leave_type_id',1) //leave_type_id is seeded as 1 for personal leave
                                            ->groupBy('employee_id')
                                            ->get();

        // dd($carryOverLeaveList);

        $carryOverLeaveList = collect($carryOverLeaveList)->map(function($record) use($maxPersonalLeave, $year){
            $remainingLeave = $maxPersonalLeave - $record['days'];
            $record['days'] = min($remainingLeave,8);
            $record['year'] = $year;
            return $record;
        })->toArray();

        CarryOverLeave::upsert($carryOverLeaveList,['employee_id','year'],['days']);
        return $carryOverLeaveList;
    }
}
