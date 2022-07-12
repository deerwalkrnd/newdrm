<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\YearlyLeave;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Unit;
use App\Models\CarryOverLeave;
use App\Models\NoPunchInNoLeave;
use App\Http\Controllers\DashboardController;
use App\Helpers\NepaliCalendarHelper;
use App\Http\Controllers\DownloadController;
class LeaveReportController extends Controller
{
    protected $employee_join_month;
    protected $employee_join_year;
    protected $employee_join_date;
    protected $thisYear;
    protected $thisMonth;
    protected $today;
    protected $balanceRecords;

    private function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            $year_month = [$nepaliDateArray[0],$nepaliDateArray[1],$nepaliDate];
            return $year_month;
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }

    public function leaveBalance(Request $request){
        $result = $this->getLeaveBalanceRecords($request);
        $records = $result[0];
        $leaveTypes = $result[1];
        $leaveTypesCount = $result[2];
        $thisYear = $result[3];
        $employees = $result[4];
        $employeeSearch = $result[5];
        $units = $result[6];

        return  view('admin.leaveBalance.index',compact('records','leaveTypes','leaveTypesCount','thisYear','employees','employeeSearch','units'));
    }
    
    
    public function getLeaveBalanceRecords($request,$download=0)
    {
        // $request->d  =>year;
        // $request->u  =>unit_id;
        // $request->e  =>employee_id;
        
        $thisYearMonthDate = $this->getNepaliYear(date('Y-m-d')); //current year and month
        $this->thisYear = $thisYearMonthDate[0];
        $thisYear = $this->thisYear;
        $this->thisMonth = $thisYearMonthDate[1];
        $this->today = $thisYearMonthDate[2];
        $employeeSearch = 0;

        $units = Unit::select('id','unit_name')->get();
        
        if($request->u != NULL && $request->e != NULL){     //search by unit_id and employee_id
            $employees = Employee::where('contract_status','active')
                                    ->where('id',$request->e)
                                    ->with('unit:id,unit_name');
            if($download==1)
                $employees = $employees->get();
            else
                $employees = $employees->paginate(10)->withQueryString();

            $employeeSearch = Employee::select('id','first_name','middle_name','last_name','unit_id')
                                        ->where('id',$request->e)
                                        ->with('unit:id,unit_name')
                                        ->first();
            }
        else if($request->u != NULL){        //search by only unit_id
            $employees = Employee::where('contract_status','active')
                                    ->where('unit_id',$request->u)
                                    ->with('unit:id,unit_name');
            if($download==1)
                $employees = $employees->get();
            else
                $employees = $employees->paginate(10)->withQueryString(); 
        }
        else if($request->e != NULL){       //search by only employee_id
            $employees = Employee::where('contract_status','active')
                                    ->where('id',$request->e)
                                    ->with('unit:id,unit_name');
            if($download==1)
                $employees = $employees->get();
            else
                $employees = $employees->paginate(10)->withQueryString();

            $employeeSearch = Employee::select('id','first_name','middle_name','last_name','unit_id')
                                        ->where('id',$request->e)
                                        ->with('unit:id,unit_name')
                                        ->first();
        }
        elseif($request->d != NULL){        //search by only year
            $employees = Employee::where('contract_status','active')->with('unit:id,unit_name');

            if($download==1)
                $employees = $employees->get();
            else
                $employees = $employees->paginate(10)->withQueryString();
        }
        else{
            $employees = Employee::where('contract_status','active')->with('unit:id,unit_name');
            if($download==1)
                $employees = $employees->get();
            else
                $employees = $employees->paginate(3)->withQueryString();
        }

        $leaveTypes = LeaveType::select('name','id','gender')->where('status','active')->get();
        $leaveTypesCount = $leaveTypes->count();
        $records = [];

        foreach($employees as $employee)
        {
            $temp = array();
            $temp['employee_id'] = $employee->id;
            $temp['name'] = $employee->first_name." ".$employee->last_name;
            $temp['unit'] = $employee->unit->unit_name;
            $temp['leaves'] = array();
           
            $join_year_month_date = $this->getNepaliYear($employee->join_date);
            $this->employee_join_year = $join_year_month_date[0];
            $this->employee_join_month = $join_year_month_date[1];
            $this->employee_join_date = $join_year_month_date[2];

            
            if($request->d != NULL && $request->d != 1){
                $start_year = $request->d;
                $end_year = $request->d;
            }else{
                $start_year = $join_year_month_date[0];
                $end_year = $this->thisYear;
            }
            for($year=$start_year; $year <= $end_year; $year++)
            {
                $temp['exceeded_leave_days'] = 0;
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
                        
                        if($temp['leaves'][$type->name]['balance'] < 0){    //check if employee has exceeded leave days
                            $temp['exceeded_leave_days'] += $leaveTypeBalance['balance'];
                        }
                       
                    }elseif(strtolower($type->gender) == 'female' && strtolower($employee->gender) == 'female'){
                        $leaveTypeBalance = $this->getEmployeeLeaveBalance($employee,$type,$year);                        
                        $temp['leaves'][$type->name]= [
                            'allowed' => $leaveTypeBalance['allowed'],
                            'accrued' => $leaveTypeBalance['accrued'],
                            'taken' => $leaveTypeBalance['taken'],
                            'balance' => $leaveTypeBalance['balance']
                        ];
                        
                        if($temp['leaves'][$type->name]['balance'] < 0){        //check if employee has exceeded leave days
                            $temp['exceeded_leave_days'] += $leaveTypeBalance['balance'];
                        }

                    }elseif(strtolower($type->gender) == 'all'){
                        $leaveTypeBalance = $this->getEmployeeLeaveBalance($employee,$type,$year);
                        $temp['leaves'][$type->name]= [
                            'allowed' => $leaveTypeBalance['allowed'],
                            'accrued' => $leaveTypeBalance['accrued'],
                            'taken' => $leaveTypeBalance['taken'],
                            'balance' => $leaveTypeBalance['balance']
                        ];

                         if($temp['leaves'][$type->name]['balance'] < 0){       //check if employee has exceeded leave days
                            $temp['exceeded_leave_days'] += $leaveTypeBalance['balance'];
                        }
                    }

                }
                $total_unpaid_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance','employee_id')
                                        ->where('acceptance','accepted')
                                        ->where('employee_id', $employee->id)
                                        ->where('year',$year)
                                        ->whereDoesntHave('leaveType',function($query){
                                            $query->where('paid_unpaid','1');
                                        })
                                        ->sum('days');
            
                $temp['total_unpaid_leaves'] = $total_unpaid_leaves;
                array_push($records,$temp);
            }         
        }
        
        //check if the record is for download or display
        if($download == 1)
            return $records;
        else
            return [$records,$leaveTypes,$leaveTypesCount,$thisYear,$employees,$employeeSearch,$units];
    }

    private function getEmployeeLeaveBalance($employee,$type,$year){
        $dashboardController = new DashboardController;
        
        //gives carryover
        $allowedLeave = $dashboardController->getAllowedLeaveDays($employee->unit_id,$type->id,$year,$employee->id);
        $acquiredMonth = 0;

        if($this->employee_join_year == $year){
            $remaining_month = 13-$this->employee_join_month;
            $allowedLeave = round(($allowedLeave/12*$remaining_month)*2)/2;
            $acquiredMonth = $this->thisMonth - $this->employee_join_month + 1;      
        }

        $acquiredLeave = $this->getAcquiredLeave($type,$allowedLeave,$acquiredMonth,$year);
        
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

    private function getAcquiredLeave($type,$allowedLeave,$acquiredMonth,$year){
        $acquiredLeave = $allowedLeave;

        if($year == $this->thisYear){
            if($type->id != '2' && $type->id != '13' && $type->id != '6' && $type->id != '10'){
                $acquiredLeave = round($allowedLeave / 12 * $this->thisMonth * 2) / 2;

                if($this->employee_join_year == $year){
                    $acquiredLeave = round($allowedLeave / 12 * $acquiredMonth * 2) / 2;
                }

            }
        }
       
        //if join date is greater than today, acquired leave is 0
        if($this->employee_join_date > $this->today){
            $acquiredLeave = 0;
        }

        return $acquiredLeave;
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
        return view('admin.report.employeesOnLeave')->with(compact('acceptedRequests'));
    }

    public function noPunchInNoLeave(Request $request)
    {
        $code = 'OXqSTexF5zn4uXSp';

        $records = NoPunchInNoLeave::select('id','employee_id','date')->with('employee:id,first_name,middle_name,last_name,manager_id');
        if(isset($request->e))
            $records =  $records->where('employee_id',$request->e);
        elseif(isset($request->d))
            $records =  $records->whereDate('date',$request->d);
        else
            $records = $records->whereDate('date',date('Y-m-d'));
                
        $records = $records->orderBy('date','desc')->get();

        $employeeSearch = Employee::select('id','first_name','middle_name','last_name')->where('id',$request->e)->get();
        
        return view('admin.report.noPunchInNoLeave')->with(compact('records','code','employeeSearch'));
    }
}
