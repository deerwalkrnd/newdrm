<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ManagerRequest;
use App\Models\Manager;
use App\Models\Employee;
use App\Models\User;

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
        $input = $request->validated();
        Manager::create($input);
        //update user role according to manager status
        $employee = [];
        $employee_id = Manager::select('employee_id')->where('employee_id',$request->employee_id)->first()->employee_id;
            
        if(strtolower($input['is_active']) == 'active')
            $employee['role_id'] = '2'; //2-manager
        else
            $employee['role_id'] = '3'; //3-employee
        
        $user = User::where('employee_id',$employee_id)->first();
        $user->update($employee);
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
        $employees_under_manager  = Employee::select('id','manager_id','first_name')->where('manager_id',$manager->employee_id)->get();
        // dd(count($employees_under_manager));

        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');
        if($input['employee_id'] != $manager->employee_id)
            if(count($employees_under_manager)>0){
                foreach($employees_under_manager as $employee_under_manager){
                    $employee_under_manager->update(['manager_id' => $input['employee_id'],'manager_change_date'=> date('Y-m-d')]);   
                }
            }
            
        $manager->update($input);
        
        //update user role according to manager status
        $employee = [];
        $employee_id = $manager->employee_id;

        if(strtolower($manager->is_active) == 'active')
            $employee['role_id'] = '2';
        else
            $employee['role_id'] = '3';

        $user = User::where('employee_id',$employee_id)->first();
        $user->update($employee);
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
            $employee = [];
            $employee_id = $manager->employee_id;
            $employees_under_manager  = Employee::select('id','manager_id','first_name')->where('manager_id',$manager->employee_id)->get()->count();

            //update user role and employees manager_id status under particular manager according to manager deletion
            if($employees_under_manager == 0){
                $manager->delete();
                $employee['role_id'] = '3';  //3-employee
                $user = User::where('employee_id',$employee_id)->first();
                $user->update($employee);

                 $res = [
                    'title' => 'Manager Deleted',
                    'message' => 'Manager has been successfully Deleted',
                    'icon' => 'success'
                ];
            }else{
                 $res = [
                    'title' => 'Manager Deletion Failed',
                    'message' => 'Manager in charge of Employees cannot be deleted.',
                    'icon' => 'warning'
                ];
            }          
            return redirect('/manager')->with(compact('res'));
        }
        catch(\Illuminate\Database\QueryException $e){
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }
}
