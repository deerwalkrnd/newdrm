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

        $lists = [];
        // return $leaveTypes;
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

            $lists[$leaveType->name] = [
                'allowed' => $allowedLeave,
                'accrued' => $acquiredLeave,
                'taken' => $leaveTaken,
                'balance' => $balance
            ];
        }

        // foreach($lists as $leaveName => $data)
        // {
        //     echo $leaveName;
        //     print_r($data['allowed']);
        // }
        // return $lists;
        return  view('admin.leaveBalance.index',compact('lists','leaveTypes'));
    }

    private function getAllowedLeaveDays($org_id,$leaveType,$year)
    {
        // dd($org_id,$leaveType,$year);
        $allowedLeave = YearlyLeave::select('days')
                                ->where('year',$year)
                                ->where('organization_id',$org_id)
                                ->where('leave_type_id',$leaveType)
                                ->where('status','active')
                                ->get()->first();
        
        // dd($allowedLeave->exists());
        if(isset($allowedLeave) && ($allowedLeave->exists() == 1))
            $allowedLeave = $allowedLeave->days;
        else
            $allowedLeave = 0;

        return $allowedLeave;
    }

}
