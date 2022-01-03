<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;
use App\Models\Employee;

class LeaveReportController extends Controller
{
    public function leaveBalance()
    {
        return  view('admin.leaveBalance.index');

        $employees = Employee::where('contract_status','active')->get();

        $leaveTypes = LeaveType::select('name')->where('gender','all')->get();
        // dd($leaveTypes);
        $record = [];

        foreach($employees as $employee)
        {
            $temp = array();
            $temp['name'] = $employee->first_name." ".$employee->last_name;
            $temp['leaves'] = array();
            for($year=date('Y',strtotime($employee->join_date)); $year <= date('Y'); $year++)
            {
                $temp['leaves'] = array($year => array());
                // dd($temp);
                foreach($leaveTypes as $type)
                {
                    $temp['leaves'][$year][$type->name] = '2';
                }

                // dd($temp);

                $yearlyLeave = YearlyLeave::select('days','leave_type_id')->with('leaveType:id,name')->where('year',$year)->get();
                echo($yearlyLeave);
                // echo($year."<br>");
            }
            array_push($record,$temp);
        }

        dd("Here",$record);
        return  view('admin.leaveBalance.index',compact());
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

    public function employeesOnLeave(Request $request){
        if(isset($request->d))
            $date = $request->d;
        else
            $date = date('Y-m-d');

        $acceptedRequests = LeaveRequest::select('id','days','leave_type_id','full_leave','start_date','end_date','employee_id','half_leave','reason','acceptance')
                                    ->where('acceptance','accepted')
                                    ->with('employee:id,first_name,last_name,middle_name')
                                    ->with('leaveType:id,name')
                                    ->whereDate('end_date', '>=', $date)
                                    ->whereDate('start_date','<=',$date)        
                                    ->get();
        return view('admin.report.employeesOnLeave')->with(compact('acceptedRequests'));;
    }

    public function noPunchInNoLeave()
    {
        $noRecordList = Employee::select('id','first_name','last_name','middle_name','manager_id','contract_status')
                ->with('manager:id,first_name,last_name')
                ->where('contract_status','active')
                ->whereDoesntHave('attendances', function ($query) {
                        $query->whereDate('created_at', date('Y-m-d'));
                    })
                ->whereDoesntHave('leaveRequest', function($query){
                    $query->whereDate('start_date','<=',date('Y-m-d'))
                        ->whereDate('end_date','>=',date('Y-m-d'));
                })
                ->get();

        return view('admin.report.noPunchInNoLeave')->with(compact('noRecordList'));
    }
}
