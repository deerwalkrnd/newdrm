<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;
use App\Models\Employee;
use App\Http\Controllers\DashboardController;
use App\Helpers\NepaliCalendarHelper;

class LeaveReportController extends Controller
{
    public function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            return $nepaliDateArray[0];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
    public function leaveBalance(Request $request)
    {
        // return  view('admin.leaveBalance.index');
        // dd($request->e);
        
        //this year
        $thisYear = $this->getNepaliYear(date('Y-m-d'));
       
        if(isset($request->e))
            $employees = Employee::where('contract_status','active')->where('id',$request->e)->get();
        else
            $employees = Employee::where('contract_status','active')->get();

        // dd($employees);
        $leaveTypes = LeaveType::select('name','id','gender')->get();
        $leaveTypesCount = $leaveTypes->count();
        $records = [];
        $dashboardController = new DashboardController;

        foreach($employees as $employee)
        {
            $temp = array();
            $temp['name'] = $employee->first_name." ".$employee->last_name;
            $temp['leaves'] = array();
            // dd($request->d);
            if(isset($request->d)){
                $start_year = $request->d;
                $end_year = $request->d;
            }else{
                $start_year = $this->getNepaliYear($employee->join_date);
                $end_year = $thisYear;
            }
            for($year=$start_year; $year <= $end_year; $year++)
            {
                $temp['leaves']['year'] = $year;
                foreach($leaveTypes as $type)
                {
                    if(strtolower($type->gender) == 'male' && strtolower($employee->gender) == 'male'){  
                        $leaveTypeBalance = $this->getEmployeeLeaveBalance($employee,$type,$year);
                        $temp['leaves'][$type->name]= [
                            'allowed' => $leaveTypeBalance['allowed'],
                            'accrued' => $leaveTypeBalance['accrued'],
                            'taken' => $leaveTypeBalance['taken'],
                            'balance' => $leaveTypeBalance['balance']
                        ];
                       
                    }elseif(strtolower($type->gender) == 'female' && strtolower($employee->gender) == 'female'){
                        $leaveTypeBalance = $this->getEmployeeLeaveBalance($employee,$type,$year);
                        $temp['leaves'][$type->name]= [
                            'allowed' => $leaveTypeBalance['allowed'],
                            'accrued' => $leaveTypeBalance['accrued'],
                            'taken' => $leaveTypeBalance['taken'],
                            'balance' => $leaveTypeBalance['balance']
                        ];
                    }elseif(strtolower($type->gender) == 'all'){
                        $leaveTypeBalance = $this->getEmployeeLeaveBalance($employee,$type,$year);
                        $temp['leaves'][$type->name]= [
                            'allowed' => $leaveTypeBalance['allowed'],
                            'accrued' => $leaveTypeBalance['accrued'],
                            'taken' => $leaveTypeBalance['taken'],
                            'balance' => $leaveTypeBalance['balance']
                        ];
                    }
                }
                $yearlyLeave = YearlyLeave::select('days','leave_type_id')
                                    ->with('leaveType:id,name')
                                    ->where('year',$year)
                                    // ->where()
                                    ->get();
                array_push($records,$temp);
            }
        }
        // dd("records",$records);
        return  view('admin.leaveBalance.index',compact('records','leaveTypes','leaveTypesCount','thisYear'));
    }

    private function getEmployeeLeaveBalance($employee,$type,$year){
        $dashboardController = new DashboardController;
        //gives carryover
        $allowedLeave = $dashboardController->getAllowedLeaveDays($employee->unit_id,$type->id,$year);

        //for carryover = 0
        // $allowedLeave = $this->getAllowedLeaveDays($employee->unit_id,$type->id,$year);
        $acquiredLeave = $allowedLeave;
        
        $fullLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave')
                                    ->where('acceptance','accepted')
                                    ->where('year',$year)
                                    ->where('employee_id',$employee->id)
                                    ->where('leave_type_id',$type->id)
                                    ->where('full_leave',"1")
                                    ->sum('days');

        $halfLeaveTaken = LeaveRequest::select('id','days','leave_type_id','full_leave')
                                    ->where('acceptance','accepted')
                                    ->where('year',$year)
                                    ->where('employee_id', $employee->id)
                                    ->where('leave_type_id',$type->id)
                                    ->where('full_leave',"0")
                                    ->sum('days');

        $leaveTaken = $fullLeaveTaken + 0.5 * $halfLeaveTaken;
        $balance = $acquiredLeave - $leaveTaken;

        $lists=[
             'allowed' => $allowedLeave,
            'accrued' => round($acquiredLeave,2),
            'taken' => $leaveTaken,
            'balance' => round($balance,2)
        ];

        return $lists;
    }

    private function getAllowedLeaveDays($unit_id,$leaveType,$year)
    {
        // dd($org_id,$leaveType,$year);
        $allowedLeave = YearlyLeave::select('days')
                                ->where('year',$year)
                                ->where('unit_id',$unit_id)
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
