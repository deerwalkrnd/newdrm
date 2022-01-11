<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\YearlyLeave;
use App\Models\Unit;
use App\Models\LeaveType;
use App\Http\Requests\YearlyLeaveRequest;

class YearlyLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
    {
        if(isset($request->y))
            $year = $request->y;
        else
            $year = date('Y');

        // dd($year);
        $yearlyLeaves = YearlyLeave::select('id', 'unit_id','leave_type_id','days','status','year')
                        ->with('unit:id,unit_name')
                        ->with('leaveType:id,name')
                        ->where('year',$year)
                        ->orderBy('unit_id')
                        ->get();
        // dd($yearlyLeaves);
        return view('admin.yearlyLeave.index')->with(compact('yearlyLeaves'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units = Unit::select('id','unit_name')->get();
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.yearlyLeave.create')->with(compact('units','leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(YearlyLeaveRequest $request)
    {
        // dd($request->validated());
        try{
            YearlyLeave::create($request->validated());
            $res = [
                'title' => 'Yearly Leave Created',
                'message' => 'Yearly Leave has been successfully Created',
                'icon' => 'success'
            ];
            return redirect('/yearly-leaves')->with(compact('res'));

        }catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
               $res = [
                'title' => 'Yearly Leave Creation Fail',
                'message' => 'Duplicate Yearly Leave Details',
                'icon' => 'error'
            ];
            return redirect('/yearly-leaves')->with(compact('res'));
            }
        }
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
        $yearlyLeaves = YearlyLeave::select('id', 'unit_id','leave_type_id','days','status','year')->findOrFail($id);
        $units = Unit::select('id','unit_name')->get();
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.yearlyLeave.edit')->with(compact('yearlyLeaves','units','leaveTypes'));
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
        $res = [
            'title' => 'Yearly Leave Updated',
            'message' => 'Yearly Leave has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/yearly-leaves')->with(compact('res'));
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
