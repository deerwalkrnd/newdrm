<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveType;
use App\Http\Requests\LeaveTypeRequest;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaveTypes = LeaveType::select('id', 'name','paid_unpaid','include_holiday')
                        ->orderBy('name')
                        ->get();
        // dd($leaveTypes);
        return view('admin.leaveType.index')->with(compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.leaveType.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveTypeRequest $request)
    {
        LeaveType::create($request->validated());
        $res = [
            'title' => 'Leave Type Created',
            'message' => 'Leave Type has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/leaveType')->with(compact('res'));
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
        $leaveType = LeaveType::select('id', 'name', 'gender','paid_unpaid','include_holiday')->findOrFail($id);
        return view('admin.leaveType.edit')->with(compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveTypeRequest $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $leaveType->update($input);
        $res = [
            'title' => 'Leave Type Updated',
            'message' => 'Leave Type has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/leaveType')->with(compact('res'));
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
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->delete();
            $res = [
                'title' => 'Leave Type Deleted',
                'message' => 'Leave Type has been successfully Deleted',
                'icon' => 'success'
            ];
            return redirect('/leaveType')->with(compact('res'));
        }catch(\Illuminate\Database\QueryException $e){
            // dd($e,$e->getCode(),$e->getCode()=="23000");
            if($e->getCode() == "23000"){
                 $res = [
                    'title' => 'Deletion Failed',
                    'message' => 'Leave Type is being Used',
                    'icon' => 'error'
                ];
                return redirect('/leaveType')->with(compact('res'));
            }
        }
       
    }
}
