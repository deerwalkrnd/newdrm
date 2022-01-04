<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\YearlyLeave;
use App\Http\Requests\LeaveRequestRequest;
use App\Http\Requests\SubordinateLeaveRequestRequest;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index()
    {
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name','leaveType:id,name'])
        ->where('employee_id',\Auth::user()->employee_id)
        ->orderBy('created_at')
        ->orderBy('updated_at')
        ->get();
        $table_title = 'Employee Leave Details';
        
        return view('admin.leaveRequest.index')->with(compact('leaveRequests','table_title'));
    }

    public function leaveDetail(Request $request)
    {
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name,manager_id','leaveType:id,name'])
        ->with('accepted_by_detail:id,first_name,last_name')
        ->where('acceptance','accepted')
        // ->orWhere('acceptance','rejected')
        ->orderBy('created_at')
        ->orderBy('updated_at');
        // dd($leaveRequests->get());

        if($request->d){
            $leaveRequests = $leaveRequests->where('start_date',$request->d)->get();
        }else{
            $leaveRequests = $leaveRequests->orderBy('start_date')->get();
        }

        $employeeSearch = Employee::select('id','first_name','middle_name','last_name')->where('contract_status','active')->get();
        $table_title = 'Employee Leave Details Lists';
        
        return view('admin.leaveRequest.leave_details')->with(compact('leaveRequests','table_title','employeeSearch'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.leaveRequest.create')->with(compact('leaveTypes'));
    }

    public function createSubOrdinateLeave(){
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.leaveRequest.createSubOrdinateLeave')->with(compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveRequestRequest $request)
    {
        $data = $request->validated();
        $leave_type_id = $data['leave_type_id'];
        $requested_leave_days = $data['days'];
        $allowed_leave = YearlyLeave::select('days')->where('leave_type_id',$leave_type_id)->where('unit_id',\Auth::user()->employee->unit_id)->get()->first()->days;
        $data['employee_id'] = \Auth::user()->employee_id;
        $data['requested_by'] = \Auth::user()->employee_id;
        $data['year'] = date('Y',strtotime($data['start_date']));
        
        if($data['leave_time'] == 'full')
        {
            $data['full_leave'] = '1';
        }else{
            $data['full_leave'] = '0';
            if($data['leave_time'] == 'first'){
                $data['half_leave'] = 'first';
            }else{
                $data['half_leave'] = 'second';
            }
        }
        // $remaining_leave = $this->calculateRemainingTime($allowed_leave,$leave_type_id,$requested_leave_days,$data['employee_id']);
       
        // if(!$remaining_leave){
        //   return redirect('/leave-request/create');
        // }
        LeaveRequest::create($data);
        return redirect('/leave-request');
    }

    public function storeSubOrdinateLeave(SubordinateLeaveRequestRequest $request)
    {
        $data = $request->validated();
        $data['employee_id'] = $data['employee_id'];
        $data['requested_by'] = \Auth::user()->employee_id;
        
        if($data['leave_time'] == 'full')
        {
            $data['full_leave'] = '1';
        }else{
            $data['full_leave'] = '0';
            if($data['leave_time'] == 'first'){
                $data['half_leave'] = 'first';
            }else{
                $data['half_leave'] = 'second';
            }
        }
        // dd($data);

        LeaveRequest::create($data);
        return redirect('/leave-request');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.leaveRequest.edit')->with(compact('leaveRequest','leaveTypes'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveRequestRequest $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $input = $request->validated();
        
        $leaveRequest->update($input);
        return redirect('/leave-request/details');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leaveRequest = LeaveRequest::where('acceptance','pending')->findOrFail($id);
        $leaveRequest->delete();
        return redirect('/leave-request');
    }

    public function accept($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id)
        ->update([
            'acceptance' => 'accepted',
            'accepted_by' => \Auth::user()->employee_id
        ]);

        return redirect('/leave-request/approve');
    }

    public function reject($id)
    {
        LeaveRequest::findOrFail($id)
        ->update([
            'acceptance' => 'rejected',
            'accepted_by' => \Auth::user()->employee_id
        ]);

        return redirect('/leave-request/approve');
    }

    private function calculateRemainingTime($allowed_leave,$leave_type_id,$requested_leave_days,$user_id){
        $year = date('Y');
        $already_taken_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance','full_leave')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', $user_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->sum('days');

        $remaining_leave = $allowed_leave - $already_taken_leaves;

        return $remaining_leave ;
    }

    public function approve(Request $request)
    {
        if(\Auth::user()->role->authority == 'employee')
            return abort(401);

        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name,manager_id','leaveType:id,name'])
        ->where('acceptance','pending');

        // dd("Here");

        if(\Auth::user()->role->authority == 'manager'){
            $leaveRequests = $leaveRequests->whereHas('employee',function($query){
                $query->where('manager_id',\Auth::user()->employee_id);
            });
        }


      
        if($request->d)
            $leaveRequests = $leaveRequests->where('start_date',$request->d)->orderBy('created_at')->orderBy('updated_at')->get();
        else
            $leaveRequests = $leaveRequests->orderBy('start_date')->orderBy('created_at')->orderBy('updated_at')->get();
        $table_title = 'Leave Applications';
        // dd($leaveRequests[0]->employee->manager);
        return view('admin.leaveRequest.approve_leave')->with(compact('leaveRequests','table_title'));
    }
}
