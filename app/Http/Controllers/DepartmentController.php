<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Unit;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::select('id','name','unit_id')
                        ->with('unit:id,unit_name')
                        ->get();
        // dd($departments);    
        return view('admin.department.index')->with(compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units = Unit::select('id','unit_name')->get();
        return view('admin.department.create')->with(compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());
        // dd($input);
        $res = [
            'title' => 'Department Created ',
            'message' => 'Department has been successfully Created ',
            'icon' => 'success'
        ];
        return redirect('/department')->with(compact('res'));
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
        $department = Department::findOrFail($id);
        $units = Unit::select('id','unit_name')->get();
        return view('admin.department.edit')->with(compact('department','units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::findOrFail($id);
        $input = $request->validated();

        $department->update($input);
        $res = [
            'title' => 'Department Updated ',
            'message' => 'Department has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/department')->with(compact('res'));
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
            $department = Department::findOrFail($id);
            $department->delete();
            $res = [
                'title' => 'Department Deleted ',
                'message' => 'Department has been successfully Deleted ',
                'icon' => 'success'
            ];
            return redirect('/department')->with(compact('res'));
        }
        catch(\Illuminate\Database\QueryException $e){
             if($e->getCode() == "23000"){
                $res = [
                    'title' => 'Department Deletion Failed',
                    'message' => 'Department cannot be deleted as it is in Use.',
                    'icon' => 'warning'
                ];
                return redirect('/department')->with(compact('res'));
            }
        }
    }
}
