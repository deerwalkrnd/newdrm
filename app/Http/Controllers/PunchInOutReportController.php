<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class PunchInOutReportController extends Controller
{
    public function getPunchInOut(Request $request){

        $employees = Employee::select('id','first_name','middle_name','last_name','manager_id')->where('contract_status','active')->with(['attendances'=>function($query) use($request){

            $query->select('id', 'employee_id', 'punch_in_time', 'punch_in_ip', 'late_punch_in', 'punch_out_time', 'punch_out_ip', 'missed_punch_out', 'reason');
            if(isset($request->e))
                $query->where('employee_id',$request->e)->get();
            elseif(isset($request->d))
                $query->whereDate('punch_in_time',$request->d)->get();
            else
                $query->whereDate('punch_in_time',date('Y-m-d'))->get();

    }])
        ->with('manager:id,first_name,middle_name,last_name')
        // ->orderBy('punch_in_time','desc')
        ->get();

        // dd($employees);

        $employeeSearch = Employee::select('id','first_name','middle_name','last_name')->where('contract_status','active')->get();
        return view('admin.report.punchInOut')->with(compact('employees','employeeSearch'));
    }

    public function latePunchInOut(Request $request){
        if($request->d)
            $date = $request->d;
        else
            $date =  date('Y-m-d');
        
        $latePunchInOuts =  Attendance::select('id','employee_id','punch_in_time','punch_in_ip','punch_out_time','punch_out_ip','missed_punch_out','late_punch_in','reason','created_at')
                    ->orWhere(['late_punch_in'=>'1','missed_punch_out'=>'1'])
                    ->whereDate('created_at',$date)
                    ->get();
        // dd($latePunchInOuts);
        return view('admin.report.latePunchInOut')->with(compact('latePunchInOuts'));
    }
}
