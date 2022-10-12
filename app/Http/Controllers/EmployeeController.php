<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;
use App\Mail\EmployeeCredentialMail;

use App\Models\Organization;
use App\Models\Unit;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Province;
use App\Models\District;
use App\Models\ServiceType;
use App\Models\Shift;
use App\Models\Role;
use App\Models\User;
use App\Models\MailControl;
use App\Models\EmergencyContact;
use App\Models\Manager;
use App\Models\CarryOverLeave;
use App\Helpers\NepaliCalendarHelper;
use App\Helpers\MailHelper;
use App\Helpers\Helper;
use App\Helpers\CalendarHelper;

use App\Actions\Fortify\CreateNewUser;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$download=0)
    {
        // $request->u => unit_id $request->m => nepali birth month
        $date= date('Y-m-d');
        $employees = Employee::select('id', 'first_name','middle_name','last_name','manager_id','service_type','designation_id','organization_id','unit_id','department_id','intern_trainee_ship_date','join_date','date_of_birth')
                                ->with('designation:id,job_title_name')
                                ->with('organization:id,name')
                                ->with('unit:id,unit_name')
                                ->with('department:id,name,unit_id')
                                ->with('serviceType:id,service_type_name')
                                // ->with('attendances:id,employee_id,punch_in_time,created_at')
                                ->where('contract_status','active')
                                ->withCount(['attendances'=>function ($query) use ($date) {
                                    $query->whereDate('punch_in_time', $date);
                                }])
                                ->orderBy('first_name') 
                                ->orderBy('last_name');

        if($request->u && $request->m){
            $employees = $employees->where('unit_id',$request->u);
            $employees = $this->getEmployeesByBirthMonth($employees,$request->m);
        }                                
        if($request->u)
            $employees = $employees->where('unit_id',$request->u);

        if($request->m){
            $employees = $this->getEmployeesByBirthMonth($employees,$request->m);
            

            // $employees = $employees->selectRaw("DATE_FORMAT(date_of_birth, '%m-%d') as DOB")
            //                         ->having('DOB', '>=', $english_start_month_day[1].'-'.$english_start_month_day[2])
            //                         ->having('DOB', '<=', $english_end_month_day[1].'-'.$english_end_month_day[2]);

        }

        $employees = $employees->get();

        $join_year =[];

        foreach ($employees as $employee){
            array_push($join_year, Helper::getNepaliYear($employee->join_date)[0]);
        }

        
        $units = Unit::select('id','unit_name')->get();
        $months = Helper::getNepaliMonthList();

        $code = 'OXqSTexF5zn4uXSp';

      
        $res = [
            'title' => 'Employee welcome ',
            'message' => 'Employee has been successfully Created ',
            'icon' => 'success'
        ];

        if($download == 1)
            return [$employees,$join_year];
        else
            return view('admin.employee.index')->with(compact('employees','join_year','res','code','units','months'));
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
        $departments = Department::select('id','name','unit_id')->get();
        $designations = Designation::select('id','job_title_name')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name','time_required')->get();
        $roles = Role::select('id','authority')->where('id','!=','2')->get();
        $managers = Manager::select('id','employee_id')
                    ->with('employee:id,first_name,middle_name,last_name,contract_status')
                    ->whereHas('employee',function($query){
                        $query->where('contract_status','active');
                    })
                    ->where('is_active','active')->get();
        return view('admin.employee.create')->with(compact('managers','units','departments','organizations','designations','provinces','districts','serviceTypes','shifts','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $input = $request->validated();
        $input['unit_id'] = Department::findOrFail($input['department_id'])->unit_id;

        // reset temporary address
        if($input['temp_add_same_as_per_add'] == 1)
        {
            unset(
                $input['temporary_address'],
                $input['temporary_district'],
                $input['temporary_municipality'],
                $input['temporary_ward_no'],
                $input['temporary_tole']
            );            
        }

        $shift = Shift::findOrFail($input['shift_id']);
        if(!$shift->time_required){
            unset(
                $input['start_time'],
                $input['end_time']
            );
        }

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
            $employee = Employee::create($input);
            $user['employee_id'] = $employee->id;
            $emergency_contact['employee_id']=$user['employee_id'];
            $createUser = new CreateNewUser();
            $created_user = $createUser->create($user);
            EmergencyContact::create($emergency_contact);
            
            $carryOverLeave = [
                'employee_id' => $user['employee_id'],
                'year' => date('Y') - 1,
                'days' => 0
            ];

            CarryOverLeave::create($carryOverLeave);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/employee/create');
        }
        $res = [
            'title' => 'Employee Created ',
            'message' => 'Employee has been successfully Created ',
            'icon' => 'success'
        ];
        // dd($input,$created_user);
        $send_mail = MailControl::select('send_mail')->where('name','Employee Credentials')->first()->send_mail;
        
        if($send_mail){
            Mail::to($employee->email)->send(new EmployeeCredentialMail($created_user));
        }
        return redirect('/employee')->with(compact('res'));
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
        $departments = Department::select('id','name','unit_id')->get();
        $designations = Designation::select('id','job_title_name')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name','time_required')->get();
        $user = User::select('id','username')->where('employee_id',$id)->get();
        $roles = Role::select('id','authority')->where('id','!=','2')->get();
        $managers = Manager::select('id','employee_id')->with('employees:id,first_name,middle_name,last_name')->where('is_active','active')->get();
        return view('admin.employee.edit')->with(compact('employee','user','departments','organizations','units','designations','provinces','districts','serviceTypes','shifts','roles','managers'));
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
        $user = User::where('employee_id',$id)->first();
        $input = $request->validated();
        
        if($user['role_id'] == '2' && $input['role'] != 1)
            $input['role']=2;

        $input['unit_id'] = Department::findOrFail($input['department_id'])->unit_id;

        // reset temporary address
        if($input['temp_add_same_as_per_add'] == 1)
        {
            unset(  $input['temporary_address'],
                    $input['temporary_district'],
                    $input['temporary_municipality'],
                    $input['temporary_ward_no'],
                    $input['temporary_tole']
                );            
        }

        //shift setting
        $shift = Shift::findOrFail($input['shift_id']);
        if(!$shift->time_required){
                $input['start_time']=NULL;
                $input['end_time']=NULL;
        }

        // dd($employee['manager_id'], $input['manager_id'], $employee['designation_id'], $input['designation_id']);
        //update manager change date/ designation change date
        if(array_key_exists('manager_id',$input))
            if($employee['manager_id'] != $input['manager_id'])
                $employee['manager_change_date'] = date('Y-m-d');

        if($employee['designation_id'] != $input['designation_id'])
            $employee['designation_change_date'] = date('Y-m-d');
        
        if($employee['department_id'] != $input['department_id'])
            $employee['department_change_date'] = date('Y-m-d');

        $userData = [];
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
        // $user
        if($user->username != $request->username)
            $userData['username'] = $input['username'];
        else    
            unset($input['username']);

        $userData['role_id'] = $input['role'];
        
        $emergency_contact['first_name'] = $input['emg_first_name'];
        $emergency_contact['last_name'] =  $input['emg_last_name'];
        $emergency_contact['middle_name'] =  $input['emg_middle_name'];
        $emergency_contact['relationship'] =  $input['emg_relationship'];
        $emergency_contact['phone_no'] = $input['emg_contact'];
        $emergency_contact['alternate_phone_no'] =  $input['emg_alternate_contact'];
        $emergency_contact['employee_id'] = $id;
     
        unset($input['image'], $input['cv'], $input['username'], $input['role']);
        unset($input['emg_first_name'],$input['emg_last_name'],$input['emg_middle_name'],$input['emg_contact'],$input['emg_alternate_contact'],$input['emg_relationship']);
        
        DB::beginTransaction();
        try {
            $employee->update($input);
            $user->update($userData);
            EmergencyContact::updateOrCreate(
                $emergency_contact,
                ['employee_id' => $id]
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/employee');
        }
        $res = [
            'title' => 'Employee Updated ',
            'message' => 'Employee has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/employee')->with(compact('res'));
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
        if(\Auth::user()->role->authority == 'manager')
            $employees = Employee::take(50)->where('contract_status','active')->where('manager_id',\Auth::user()->employee_id)->get();
        else
            $employees = Employee::take(50)->where('contract_status','active')->get();

        if($request->has('q'))
        {
            $keyword = $request->q;
            $employees = Employee::where(DB::raw('CONCAT_WS(" ", first_name, middle_name, last_name)'),'like',"%$keyword%")->take(20)->where('contract_status','active')->get();
        }

        return response()->json($employees);
    }


    public function profile(Request $request)
    {
        if($request->id == NULL)
            $user = \Auth::user()->employee_id;
        else
            $user = (int) $request->id;


        $employee = Employee::with('designation:id,job_title_name')
                        ->with('organization:id,name')
                        ->with('unit:id,unit_name')
                        ->with('province:id,province_name')
                        ->with('district:id,district_name')
                        ->with('serviceType:id,service_type_name')
                        ->with('shift')
                        ->with('manager:id,first_name,middle_name,last_name')
                        ->with('emergencyContact:employee_id,first_name,middle_name,last_name,relationship,phone_no,alternate_phone_no')
                        ->findOrFail($user);
        // dd($employee->emergencyContact->first_name);
        return view('admin.employee.profile')->with('employee',$employee);
    }

    public function terminated(Request $request,$download=0)
    {
        $terminatedEmployees = Employee::select('id','first_name','last_name','middle_name','manager_id', 'designation_id','terminated_date','join_date')
                    ->where('contract_status','terminated')
                    ->with('designation')
                    ->with('manager:id,first_name,last_name,middle_name')
                    ->orderBy('terminated_date','desc');
                    // ->get();
        if($request->u)
            $terminatedEmployees = $terminatedEmployees->where('unit_id',$request->u);
        
        $terminatedEmployees = $terminatedEmployees->get();
        $units = Unit::select('id','unit_name')->get();
        
        if($download == 1)
            return $terminatedEmployees;
        else
            return view('admin.employee.terminate')->with(compact('terminatedEmployees','units'));
    }

    public function terminate(Request $request)
    {
        $id = (int) $request->employee_id;
        if(User::select('id','role_id')->where('employee_id',$id)->first()->role_id == '2')
            Manager::select('id','employee_id')->where('employee_id',$id)->update(['is_active'=>'inactive']);

        Employee::findOrFail($id)->update(['contract_status' => 'terminated','terminated_date' => date('Y-m-d'),]);

        $res = [
            'title' => 'Employee Terminated ',
            'message' => 'Employee has been successfully Terminated ',
            'icon' => 'success'
        ];

        return redirect('/employee/terminate')->with(compact('res'));
    }

    public function getEmployeesByBirthMonth($employees,$month){
        $current_year = Helper::getNepaliYear(date('Y-m-d'))[0];
            $calendarHelper = new CalendarHelper;

            $nepali_start_month_day = $current_year.'-'.$month.'-01';   
            $nepali_end_month_day = $current_year.'-'.$month.'-'.$calendarHelper->getLastDayOfMonth($current_year,$month);        

            $english_start_month_day = Helper::getEnglishDate($nepali_start_month_day);
            $english_end_month_day = Helper::getEnglishDate($nepali_end_month_day);

            $employees =  $employees->where(function($query) use($english_start_month_day,$english_end_month_day){
                                    $query->whereMonth('date_of_birth','>',$english_start_month_day[1])
                                            ->orWhere(function($query) use($english_start_month_day,$english_end_month_day){
                                                $query->whereMonth('date_of_birth',$english_start_month_day[1])
                                                    ->whereDay('date_of_birth','>=',$english_start_month_day[2]);
                                            });   
                                })
                                ->where(function($query) use($english_end_month_day){
                                    $query->whereMonth('date_of_birth','<',$english_end_month_day[1])
                                            ->orWhere(function($query) use($english_end_month_day){
                                                $query->whereMonth('date_of_birth',$english_end_month_day[1])
                                                    ->whereDay('date_of_birth','<=',$english_end_month_day[2]);
                                            });  
                                        
                                });
            return $employees;
    }


    public function editContact($id)
    {
        $employee = Employee::with('emergencyContact')->findOrFail($id);
        $organizations = Organization::select('id','name')->get();
        $units = Unit::select('id','unit_name')->get();
        $departments = Department::select('id','name','unit_id')->get();
        $designations = Designation::select('id','job_title_name')->get();
        $provinces = Province::select('id', 'province_name')->get();
        $districts = District::select('id', 'district_name', 'province_id')->get();
        $serviceTypes = ServiceType::select('id','service_type_name')->get();
        $shifts = Shift::select('id','name','time_required')->get();
        $user = User::select('id','username')->where('employee_id',$id)->get();
        $roles = Role::select('id','authority')->where('id','!=','2')->get();
        $managers = Manager::select('id','employee_id')->with('employees:id,first_name,middle_name,last_name')->where('is_active','active')->get();
        return view('admin.employee.contact')->with(compact('employee','user','departments','organizations','units','designations','provinces','districts','serviceTypes','shifts','roles','managers'));
    }

    public function updateContact(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $input = $request->validate([
            'mobile' => 'required|digits:10',
        ]);
        
        try {
            $employee->update($input);
        } catch (\Exception $e) {
            return redirect('/employee');
        }
        $res = [
            'title' => 'Contact Updated ',
            'message' => 'Employee\'s contact has been successfully Updated ',
            'icon' => 'success'
        ];
        return redirect('/contact')->with(compact('res'));
    }

}
