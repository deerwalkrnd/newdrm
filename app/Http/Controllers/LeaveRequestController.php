<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Http\Requests\LeaveRequestRequest;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name','leaveType:id,name'])
        ->orderBy('created_at')
        ->orderBy('updated_at')
        ->paginate(10);
        
        return view('admin.leaveRequest.index')->with(compact('leaveRequests'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveRequestRequest $request)
    {
        $data = $request->validated();
        $data['employee_id'] = \Auth::user()->id;
        
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

        $data['accepted_by'] = \Auth::user()->id;

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        LeaveRequest::findOrFail($id)
        ->update([
            'acceptance' => 'accepted',
            'accepted_by' => \Auth::user()->id
        ]);

        return redirect('/leave-request');
    }

    public function reject($id)
    {
        LeaveRequest::findOrFail($id)
        ->update([
            'acceptance' => 'rejected',
            'accepted_by' => \Auth::user()->id
        ]);

        return redirect('/leave-request');
    }
}
