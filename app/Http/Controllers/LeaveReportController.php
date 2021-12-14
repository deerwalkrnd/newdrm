<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;

class LeaveReportController extends Controller
{
    public function leaveBalance()
    {
        $year = date('Y');
        $month = date('m');
        $org_id = \Auth::user()->employee->organization_id;
        $leaveTypes = LeaveType::select('name','id')->get();

        $list = [];

        foreach($leaveTypes as $leaveType)
        {
            $allowedLeave = $this->getAllowedLeaveDays($org_id,$leaveType->id,$year);
            $acquiredLeave = $allowedLeave / 12 * $month;
            $leaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave')
                                        ->where('acceptance','accepted')
                                        ->where('employee_id', \Auth::user()->id)
                                        ->where('leave_type_id',$leaveType->id)
                                        ->sum('days');
            $balance = $allowedLeave - $leaveTaken;

            $list[$leaveType->name] = [
                'allowed' => $allowedLeave,
                'acquired' => $acquiredLeave,
                'taken' => $leaveTaken,
                'balance' => $balance
            ];
        }

        return $list;
    }

    private function getAllowedLeaveDays($org_id,$leaveType,$year)
    {
        $allowedLeave = YearlyLeave::select('days')
                                // ->where('year',$year)
                                ->where('organization_id',$org_id)
                                ->where('leave_type_id',$leaveType)
                                ->where('status','active')
                                ->first();

        if(isset($allowedLeave) && $allowedLeave->count() == 1)
            $allowedLeave = $allowedLeave->days;
        else
            $allowedLeave = 0;

        return $allowedLeave;
    }

}
