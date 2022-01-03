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
use App\Models\User;
use App\Models\EmergencyContact;
use App\Models\Manager;

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
        $employees = Employee::select('id', 'first_name','middle_name','last_name','manager_id','service_type','designation_id','organization_id','unit_id','intern_trainee_ship_date','join_date')
        ->with('designation:id,job_title_name')
        ->with('organization:id,name')
        ->with('unit:id,unit_name')
        ->with('serviceType:id,service_type_name')
        ->orderBy('first_name') 
        ->get();

        // dd($employees);
        
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
        $managers = Manager::select('id','employee_id')->with('employees:id,first_name,middle_name,last_name')->get();
        // dd($managers);

        return view('admin.employee.create')->with(compact('managers','units','organizations','designations','provinces','districts','serviceTypes','shifts','roles'));
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
        $emergency_contact =[];

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
        $user['username'] = $request->username;
        $user['role_id'] = $request->role;
       
        $emergency_contact['first_name'] = $input['emg_first_name'];
        $emergency_contact['last_name'] =  $input['emg_last_name'];
        $emergency_contact['middle_name'] =  $input['emg_middle_name'];
        $emergency_contact['relationship'] =  $input['emg_relationship'];
        $emergency_contact['phone_no'] = $input['emg_contact'];
        $emergency_contact['alternate_phone_no'] =  $input['emg_alternate_contact'];
     
        unset($input['image'], $input['cv'], $input['username'], $input['role']);
        unset($input['emg_first_name'],$input['emg_last_name'],$input['emg_middle_name'],$input['emg_contact'],$input['emg_alternate_contact'],$input['emg_relationship']);
 
        DB::beginTransaction();
        try {
            $user['employee_id'] = Employee::create($input)->id;
            $emergency_contact['employee_id']=$user['employee_id'];
            $createUser = new CreateNewUser();
            $createUser->create($user);
            
            EmergencyContact::create($emergency_contact);
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
        $employee = Employee::with('emergencyContact')->findOrFail($id);
        $organizations = Organization::select('id','name')->get();
        $units = Unit::select('id','unit_name')->get();
        $designations = Designation::select('id','job_title_name')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name')->get();
        // $emergency_contacts = EmergencyContact::select('id','first_name','last_name','middle_name','relationship','phone_no','alternate_phone_no')->where('employee_id',$id)->get();
        $emergencyContact = EmergencyContact::FindOrFail($id);
        $user = User::select('id','username')->where('employee_id',$id)->get();
        $roles = Role::select('id','authority')->get();
        $managers = Manager::select('id','employee_id')->with('employees:id,first_name,middle_name,last_name')->get();
        return view('admin.employee.edit')->with(compact('employee','user','organizations','units','designations','provinces','districts','serviceTypes','shifts','roles','managers','emergencyContact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $emergencyContact = EmergencyContact::where('employee_id',$employee->id)->first();

        $input = $request->validated();
        // dd($input);
        $user = [];
        $emergency_contact =[];
        //store image
        $image = $request->file('image');
        $cv = $request->file('cv');
        
        //merge files
        // if($image!=null){
        //     $input['image_name'] = $image->store('employees/images');
        // }
        // if($cv != null){
        //     $input['cv_file_name'] = $cv->store('employees/cv');
        // }

        //add data to user
        $user['username'] = $request->username;
        $user['role_id'] = $request->role;
        
        $emergency_contact['first_name'] = $input['emg_first_name'];
        $emergency_contact['last_name'] =  $input['emg_last_name'];
        $emergency_contact['middle_name'] =  $input['emg_middle_name'];
        $emergency_contact['relationship'] =  $input['emg_relationship'];
        $emergency_contact['phone_no'] = $input['emg_contact'];
        $emergency_contact['alternate_phone_no'] =  $input['emg_alternate_contact'];
     
        unset($input['image'], $input['cv'], $input['username'], $input['role']);
        unset($input['emg_first_name'],$input['emg_last_name'],$input['emg_middle_name'],$input['emg_contact'],$input['emg_alternate_contact'],$input['emg_relationship']);
 
        DB::beginTransaction();
        try {
            $employee->update($input);
            
            $emergencyContact->update($emergency_contact);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/employee/edit');
        }
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
        try{
            $employee = Employee::findOrFail($id);
            return redirect('/employee');
    
        }catch(\Illuminate\Database\QueryException $e){
            dd($e);
            if($e->getCode() == "23000"){
                return redirect()->back();
            }
        }
    }

    /**
     * Search the specified resource from storage
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */ 
    public function search(Request $request)
    {
        $employees = Employee::take(50)->get();
        if($request->has('q'))
        {
            $keyword = $request->q;
            $employees = Employee::where(DB::raw('CONCAT_WS(" ", first_name, middle_name, last_name)'),'like',"%$keyword%")->take(20)->get();
        }

        return response()->json($employees);
    }


    public function profile()
    {
        $employee = Employee::select('*')
                        ->with('designation:id,job_title_name')
                        ->with('organization:id,name')
                        ->with('unit:id,unit_name')
                        ->with('province:id,province_name')
                        ->with('district:id,district_name')
                        ->with('serviceType:id,service_type_name')
                        ->with('shift')
                        ->with('manager:id,first_name,middle_name,last_name')
                        ->with('emergencyContact:employee_id,first_name,middle_name,last_name,relationship,phone_no,alternate_phone_no')
                        ->where('id', \Auth::user()->id)
                        ->first();

        
        return view('admin.employee.profile')->with('employee',$employee);
    }

    public function terminated()
    {
        $terminatedEmployees = Employee::select('id','first_name','last_name','middle_name','manager_id', 'designation_id')
                    ->where('contract_status','terminated')
                    ->with('designation')
                    ->with('manager:id,first_name,last_name,middle_name')
                    ->get();

        return view('admin.employee.terminate')->with(compact('terminatedEmployees'));
    }

    public function terminate(Request $request)
    {
        $id = (int) $request->employee_id;
        Employee::findOrFail($id)->update(['contract_status' => 'terminated']);

        return redirect('/employee/terminate');
    }

}
