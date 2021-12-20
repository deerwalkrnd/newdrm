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
        //add the year column in leave request section
        $maxPersonalLeave = 13;
        $carryOverLeaveList = LeaveRequest::select('employee_id',\DB::raw('SUM(days) as days'))
                                            // ->where('accepted_by','!=',NULL)
                                            // ->where('year',$year-1)
                                            ->groupBy('employee_id')
                                            ->get();

        $carryOverLeaveList = collect($carryOverLeaveList)->map(function($record) use($maxPersonalLeave){
            $remainingLeave = $maxPersonalLeave - $record['days'];
            $record['days'] = min($remainingLeave,8);
            return $record;
        })->toArray();

        CarryOverLeave::upsert($carryOverLeaveList,['employee_id','year'],['days']);
        return $carryOverLeaveList;
    }
}
