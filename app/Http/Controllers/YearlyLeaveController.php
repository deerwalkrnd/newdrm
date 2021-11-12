<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\YearlyLeave;
use App\Models\Organization;
use App\Models\LeaveType;
use App\Http\Requests\YearlyLeaveRequest;

class YearlyLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
    {
        $yearlyLeaves = YearlyLeave::select('id', 'organization_id','leave_type_id','days','status')
                        ->with('organization:id,name')
                        ->with('leaveType:id,name')
                        ->orderBy('organization_id')
                        ->paginate(10);
        return view('admin.yearlyLeave.index')->with(compact('yearlyLeaves'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = Organization::select('id','name')->get();
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.yearlyLeave.create')->with(compact('organizations','leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(YearlyLeaveRequest $request)
    {
        YearlyLeave::create($request->validated());
        return redirect('/yearly-leaves');
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
        $yearlyLeaves = YearlyLeave::select('id', 'organization_id','leave_type_id','days','status')->findOrFail($id);
        $organizations = Organization::select('id','name')->get();
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.yearlyLeave.edit')->with(compact('yearlyLeaves','organizations','leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(YearlyLeaveRequest $request, $id)
    {
        $yearlyLeave = YearlyLeave::findOrFail($id);

        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $yearlyLeave->update($input);
        return redirect('/yearly-leaves');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $yearlyLeave = YearlyLeave::findOrFail($id);
            $yearlyLeave->delete();
            return redirect('/yearly-leaves');
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
