<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $curr_month = date('m');
        $curr_day = date('d');

        $next_month = date('m', strtotime("+30 days"));
        $next_day = date('d', strtotime("+30 days"));
        
        $leaveBalance = $this->getLeaveBalance();
        // $oneMonth = date('m-d', strtotime("+30 days"));
        // dd($curr_month, $curr_day, $next_day, $next_month);

        // $birthdayList = Employee::where(function($query) {
        //                             $query->whereMonth('date_of_birth', '=', $curr_month)
        //                                 ->whereDay('date_of_birth','>',$curr_day);
        //                         })
        //                         ->orWhereMonth('date_of_birth','>',$curr_month)
        //                         ->where()
        //                         ;

        $birthdayList = Employee::select('first_name','last_name','middle_name','date_of_birth')
                                ->whereMonth('date_of_birth','>',$curr_month)
                                ->orWhere(function($query) use($curr_month,$curr_day){
                                    $query->whereMonth('date_of_birth',$curr_month)
                                        ->whereDay('date_of_birth','>',$curr_day);
                                })
                                ->whereMonth('date_of_birth','<=',$next_month)
                                ->orWhere(function($query) use($next_month,$next_day){
                                    $query->whereMonth('date_of_birth',$next_month)
                                        ->whereDay('date_of_birth','<=',$next_day);
                                })->get();
        // dd($birthdayList);
        return view('admin.dashboard.index')->with(compact('leaveBalance','birthdayList'));
    }

    private function getLeaveBalance()
    {
        $year = date('Y');
        $month = date('m');
        $org_id = \Auth::user()->employee->organization_id;
        $leaveTypes = LeaveType::select('name','id')->get();

        $lists = array();
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

        return $lists;
    }

    private function getAllowedLeaveDays($org_id,$leaveType,$year)
    {
        $allowedLeave = YearlyLeave::select('days')
                                ->where('year',$year)
                                ->where('organization_id',$org_id)
                                ->where('leave_type_id',$leaveType)
                                ->where('status','active')
                                ->get()->first();
        
        if(isset($allowedLeave) && ($allowedLeave->exists() == 1))
            $allowedLeave = $allowedLeave->days;
        else
            $allowedLeave = 0;

        return $allowedLeave;
    }
}
