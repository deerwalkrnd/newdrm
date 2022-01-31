<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ManagerRequest;
use App\Models\Manager;
use App\Models\Employee;

use Illuminate\Support\Facades\DB;


class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $managers = Manager::select('id', 'employee_id','is_active')
                            ->with('employee:id,first_name,last_name')
                            ->orderBy('id')
                            ->get();
        return view('admin.manager.index')->with(compact('managers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $managers = Manager::select('id','employee_id','is_active')->get();
        $employees = Employee::select('id','first_name','middle_name','last_name')->where('contract_status','active')->get();
       
        return view('admin.manager.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManagerRequest $request)
    {
        Manager::create($request->validated());
        $res = [
            'title' => 'Manager Created',
            'message' => 'Manager has been successfully created',
            'icon' => 'success'
        ];
        return redirect('/manager')->with(compact('res'));
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
        $manager = Manager::select('id', 'employee_id', 'is_active')->findOrFail($id);
        $employees = Employee::select('id','first_name', 'last_name')->where('contract_status','active')->get();
        return view('admin.manager.edit')->with(compact('manager','employees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ManagerRequest $request, $id)
    {
        $manager = Manager::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $manager->update($input);
        $res = [
            'title' => 'Manager Updated',
            'message' => 'Manager has been successfully updated',
            'icon' => 'success'
        ];
        return redirect('/manager')->with(compact('res'));
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
            $manager = Manager::findOrFail($id);
            $manager->delete();
            $res = [
                'title' => 'Manager Deleted',
                'message' => 'Manager has been successfully Deleted',
                'icon' => 'success'
            ];
            return redirect('/manager')->with(compact('res'));
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
