<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;

use App\Models\Organization;
use App\Models\Unit;
use App\Models\Designation;
use App\Models\Province;
use App\Models\District;
use App\Models\ServiceType;
use App\Models\Shift;
use App\Models\Role;

use App\Actions\Fortify\CreateNewUser;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::select('id', 'first_name','middle_name','last_name','manager_id','organization_id','unit_id','intern_trainee_ship_date','join_date')
        ->orderBy('first_name') 
        ->paginate(10);
        
        return view('admin.employee.index')->with(compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = Organization::select('id','name')->get();
        $units = Unit::select('id','unit_name')->get();
        $designations = Designation::select('id','job_title_name')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name')->get();
        $roles = Role::select('id','authority')->get();

        return view('admin.employee.create')->with(compact('units','organizations','designations','provinces','districts','serviceTypes','shifts','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        //get validated input
        $input = $request->validated();
        $user = [];

        //store image
        $image = $request->file('image');
        $cv = $request->file('cv');
        
        //merge files
        if($image!=null){
            $input['image_name'] = $image->store('employees/images');
        }
        if($cv != null){
            $input['cv_file_name'] = $cv->store('employees/cv');
        }

        //add data to user
        // $user['organization_id'] = $request->organization_id;
        $user['username'] = $request->username;
        $user['role_id'] = $request->role;

        //remove unwanted fields
        unset($input['image'], $input['cv'], $input['username'], $input['role']);

        //use transaction to assure success or failure of both operation
        DB::beginTransaction();
        try {
            //create object based on verified input
            $user['employee_id'] = Employee::create($input)->id;
            // dd($user);
            $createUser = new CreateNewUser();
            $createUser->create($user);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/employee/create');
        }

        return redirect('/employee');
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
        $employee = Employee::findOrFail($id);
        $organizations = Organization::select('id','name')->get();
        $units = Unit::select('id','unit_name')->get();
        $designations = Designation::select('id','job_title')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name')->get();
        

        return view('admin.employee.edit')->with(compact('employee','organizations','units','designations','provinces','districts','serviceTypes','shifts'));
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
        $employee = Employee::findOrFail($id);
        
        //get validated input and merge input fields
        $input = $request->validated();
        $input['version'] = DB::raw('version+1');

        $employee->update($input);
        return redirect('/employee');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
