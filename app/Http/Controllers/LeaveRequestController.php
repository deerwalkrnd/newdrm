<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\NoPunchInNoLeave;
use App\Models\MailControl;
use App\Models\YearlyLeave;
use App\Http\Requests\LeaveRequestRequest;
use App\Http\Requests\SubordinateLeaveRequestRequest;
use App\Http\Controllers\SendMailController;
use App\Helpers\NepaliCalendarHelper;
use App\Helpers\MailHelper;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestMail;
use App\Mail\SubOrdinateLeaveRequestMail;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index()
    {
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name,manager_id','leaveType:id,name'])
        ->where('employee_id',\Auth::user()->employee_id)
        ->orderBy('start_date','desc')
        ->orderBy('created_at','desc')
        ->orderBy('updated_at','desc')
        // ->paginate(30);
        ->get();
        $table_title = 'Employee Leave Details';
        return view('admin.leaveRequest.index')->with(compact('leaveRequests','table_title'));
    }

    public function leaveDetail(Request $request)
    {
        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
        ->with(['employee:id,first_name,last_name,manager_id','leaveType:id,name'])
        ->with('accepted_by_detail:id,first_name,last_name')
        ->where('acceptance','accepted')
        // ->orWhere('acceptance','rejected')
        ->orderBy('start_date','desc')
        ->orderBy('created_at')
        ->orderBy('updated_at');
        // dd($leaveRequests->get());

        if($request->d){
            $leaveRequests = $leaveRequests->where('start_date',$request->d)->paginate(30);
        }else{
            $leaveRequests = $leaveRequests->orderBy('start_date')->paginate(30);
        }

        $employeeSearch = Employee::select('id','first_name','middle_name','last_name')->where('contract_status','active')->get();
        $table_title = 'Employee Leave Details Lists';
        
        return view('admin.leaveRequest.leave_details')->with(compact('leaveRequests','table_title','employeeSearch'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $leaveTypes = LeaveType::select('id','name')->where('status','active')->get();
        return view('admin.leaveRequest.create')->with(compact('leaveTypes'));
    }

    public function createSubOrdinateLeave(){
        $leaveTypes = LeaveType::select('id','name')->where('status','active')->get();
        return view('admin.leaveRequest.createSubOrdinateLeave')->with(compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    private function getNepaliYear($year){
        try{
            $date = new NepaliCalendarHelper($year,1);
            $nepaliDate = $date->in_bs();
            $nepaliDateArray = explode('-',$nepaliDate);
            return $nepaliDateArray[0];
        }catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
    public function store(LeaveRequestRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        $leave_type_id = $data['leave_type_id'];
        $requested_leave_days = $data['days'];
        $allowed_leave = YearlyLeave::select('days')->where('leave_type_id',$leave_type_id)->where('unit_id',\Auth::user()->employee->unit_id)->get()->first();
        if($allowed_leave)
            $allowed_leave = $allowed_leave->days;
        else
            $allowed_leave = 0;

        $data['employee_id'] = \Auth::user()->employee_id;
        $data['requested_by'] = \Auth::user()->employee_id;

        //nepali date
        $start_year = $this->getNepaliYear($data['start_date']);
        $end_year = $this->getNepaliYear($data['end_date']);
        // dd($start_year,$end_year);

        
        if($start_year != $end_year){
            $res = [
                'title' => 'Leave Request Error',
                'message' => 'Leave should be requested from same Nepali year.',
                'icon' => 'error'
            ];
            return redirect()->back()->with(compact('res'));
        }else{
            $data['year'] = $start_year;
        }

        if($data['leave_time'] == 'full')
        {
            $data['full_leave'] = '1';

            $not_eligible_dates = $this->nonEligibleFullLeaveDays($data,\Auth::user()->employee_id);
            if(!$not_eligible_dates->isEmpty()){
                $res = [
                    'title' => 'Leave Request Warning',
                    'message' => 'Leave Request has been already applied for given date. Please look into your leave deatils.',
                    'icon' => 'warning'
                ];
                return redirect('/leave-request')->with(compact('res'));
            }

        }else{
            $data['full_leave'] = '0';
            $not_eligible_dates = $this->nonEligibleFullLeaveDays($data,\Auth::user()->employee_id);
            if($data['leave_time'] == 'first'){
                $data['half_leave'] = 'first';
                if(!$not_eligible_dates->isEmpty()){
                    $res = [
                        'title' => 'Leave Request Warning',
                        'message' => 'First Half Leave Request has been already applied for given date. Please look into your leave deatils.',
                        'icon' => 'warning'
                    ];
                    return redirect('/leave-request')->with(compact('res'));
                }
            }else{
                $data['half_leave'] = 'second';
                if(!$not_eligible_dates->isEmpty()){
                $res = [
                    'title' => 'Leave Request Warning',
                    'message' => 'Second Half Leave Request has been already applied for given date. Please look into your leave deatils.',
                    'icon' => 'warning'
                ];
                return redirect('/leave-request')->with(compact('res'));
            }
            }
            
            // dd("hald leave");
        }
        dd("successfully");
       $leaveRequest = LeaveRequest::create($data);
        // dd($leaveRequest);

        //Send Mail to manager,hr and employee after successful leave request 
        $send_mail = MailControl::select('send_mail')->where('name','Leave Request')->first()->send_mail;
        $subject = "Leave Request";
        // dd(MailHelper::getHrEmail(),MailHelper::getManagerEmail($leaveRequest->employee_id),$leaveRequest->employee->email);

        $ccList=MailHelper::getHrEmail();
        array_push($ccList,$leaveRequest->employee->email);
        if($send_mail){
           Mail::to(MailHelper::getManagerEmail($leaveRequest->employee_id))
           ->cc($ccList)
            ->send(new LeaveRequestMail($leaveRequest));
            // MailHelper::sendEmail($type=1,$leaveRequest,$subject);
        }

        $res = [
            'title' => 'Leave Request Created',
            'message' => 'Leave Request has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/leave-request')->with(compact('res'));
    }

    public function storeSubOrdinateLeave(SubordinateLeaveRequestRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        $data['employee_id'] = $data['employee_id'];
        $data['requested_by'] = \Auth::user()->employee_id;
        
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
        // dd($data);
        $start_year = $this->getNepaliYear($data['start_date']);
        $end_year = $this->getNepaliYear($data['end_date']);
        
         if($start_year != $end_year){
            $res = [
                'title' => 'Leave Request Error',
                'message' => 'Leave should be requested from same Nepali year.',
                'icon' => 'error'
            ];
            return redirect()->back()->with(compact('res'));
        }else{
            $data['year'] = $start_year;
        }

        $leaveRequest = LeaveRequest::create($data);
        // dd($leaveRequest);
        //send mail
        $subject = "Subordinate Leave Request";
        $send_mail = MailControl::select('send_mail')->where('name','Subordinate Leave')->first()->send_mail;
        
        $ccList=MailHelper::getHrEmail();
        array_push($ccList,$leaveRequest->requested_by_detail->email,MailHelper::getManagerEmail($leaveRequest->employee_id));

        if($send_mail){
            Mail::to($leaveRequest->employee->email)
                ->cc($ccList)
                ->send(new SubordinateLeaveRequestMail($leaveRequest));
        }$res = [
            'title' => 'Subordinate Leave Request Created',
            'message' => 'Subordinate Leave Request has been successfully Created',
            'icon' => 'success'
        ];
        return redirect('/leave-request/approve')->with(compact('res'));
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
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveTypes = LeaveType::select('id','name')->get();
        return view('admin.leaveRequest.edit')->with(compact('leaveRequest','leaveTypes'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveRequestRequest $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $input = $request->validated();
        
        $leaveRequest->update($input);
        $res = [
            'title' => 'Leave Request Updated',
            'message' => 'Leave Request has been successfully Updated',
            'icon' => 'success'
        ];
        return redirect('/leave-request/details')->with(compact('res'));
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
        $res = [
            'title' => 'Leave Request Deleted',
            'message' => 'Leave Request has been successfully Deleted',
            'icon' => 'success'
        ];
        $role = \Auth::user()->role->authority;
        if($role == 'hr' || $role == 'manager')
            return back()->with(compact('res'));
        else
            return redirect('/leave-request')->with(compact('res'));
    }

    public function forceDestroy($id)
    {
        $leaveRequest = LeaveRequest::where('reason','Forced (System)')->findOrFail($id);
        $leaveRequest->delete();
        $res = [
            'title' => 'Forced Leave Deleted',
            'message' => 'Force Leave has been successfully Deleted',
            'icon' => 'success'
        ];
        $role = \Auth::user()->role->authority;
        if($role == 'hr' || $role == 'manager')
            return back()->with(compact('res'));
        else
            return redirect('/leave-request')->with(compact('res'));
    }

    public function accept($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update([
            'acceptance' => 'accepted',
            'accepted_by' => \Auth::user()->employee_id
        ]);

        $noPunchInNoLeaveRecord = NoPunchInNoLeave::where('employee_id',$leaveRequest->employee_id)
                                                    ->whereDate('date','<=',$leaveRequest->start_date)
                                                    ->first();
        if($noPunchInNoLeaveRecord)
            if($noPunchInNoLeaveRecord->count() >= 1)
                $noPunchInNoLeaveRecord->delete();  
        $res = [
            'title' => 'Leave Request Accepted',
            'message' => 'Leave Request has been successfully Accepted',
            'icon' => 'success'
        ];
        return redirect('/leave-request/approve')->with(compact('res'));
    }

    public function reject($id)
    {
        LeaveRequest::findOrFail($id)
        ->update([
            'acceptance' => 'rejected',
            'accepted_by' => \Auth::user()->employee_id
        ]);
          $res = [
            'title' => 'Leave Request Rejected',
            'message' => 'Leave Request has been successfully Rejected',
            'icon' => 'success'
        ];

        return redirect('/leave-request/approve')->with(compact('res'));
    }

    private function calculateRemainingTime($allowed_leave,$leave_type_id,$requested_leave_days,$user_id){
        $year = $this->getNepaliYear(date('Y-m-d'));
        $already_taken_leaves = LeaveRequest::select('id','days','leave_type_id','year','acceptance','full_leave')
                                        ->where('acceptance','accepted')
                                        ->where('year',$year)
                                        ->where('employee_id', $user_id)
                                        ->where('leave_type_id',$leave_type_id)
                                        ->sum('days');

        $remaining_leave = $allowed_leave - $already_taken_leaves;

        return $remaining_leave ;
    }

    public function approve(Request $request)
    {
        if(\Auth::user()->role->authority == 'employee')
            return abort(401);

        $leaveRequests = LeaveRequest::select('id', 'start_date', 'year', 'employee_id', 'end_date', 'days','leave_type_id', 'full_leave', 'half_leave', 'reason', 'acceptance', 'accepted_by')
                                        ->with(['employee:id,first_name,last_name,manager_id','leaveType:id,name'])
                                        ->where('acceptance','pending');

        // dd("Here");

        if(\Auth::user()->role->authority == 'manager'){
            $leaveRequests = $leaveRequests->whereHas('employee',function($query){
                $query->where('manager_id',\Auth::user()->employee_id);
            });
        }
        if($request->d)
            $leaveRequests = $leaveRequests->where('start_date',$request->d)
                                            ->orderBy('created_at')
                                            ->orderBy('updated_at')
                                            ->get();
        else
            $leaveRequests = $leaveRequests->orderBy('start_date')
                                            ->orderBy('created_at')
                                            ->orderBy('updated_at')
                                            ->get();
        // dd($leaveRequests[0]->employee->manager);
        return view('admin.leaveRequest.approve_leave')->with(compact('leaveRequests'));
    }

    public function getForcedLeave()
    {
        if(\Auth::user()->role->authority == 'hr')
        {
            $leaveList = LeaveRequest::where('reason','Forced (System)')->orderBy('end_date','desc')->paginate(20);
        }
        elseif(\Auth::user()->role->authority == 'manager'){
            $leaveList = LeaveRequest::whereHas('employee',function($query){
                                                $query->where('manager_id',\Auth::user()->employee_id);
                                        })
                                        ->where('reason','Forced (System)')
                                        ->orderBy('end_date','desc')
                                        ->paginate(20); 
        }else{
            return abort('403');
        }

        return view('admin.leaveRequest.forcedLeave')->with(compact('leaveList'));
    }

    public function getMyForcedLeave()
    {
        $leaveList = LeaveRequest::where('employee_id',\Auth::user()->employee_id)
                                    ->where('reason','Forced (System)')
                                    ->orderBy('end_date','desc')
                                    ->paginate(20);
        return view('admin.leaveRequest.myForcedLeave')->with(compact('leaveList'));
    }


    //get leave days for dyanmic days calculation in form
    public function getLeaveDays(Request $request){
        // return \Request::input('employee_id');
        if(\Request::input('employee_id'))
            $employee_id = \Request::input('employee_id');
        else
            $employee_id = \Auth::user()->employee_id;

        $today = date('Y-m-d');
        $start_date =   \Request::input('start_date');
        $end_date = \Request::input('end_date');
        $leave_type_id = \Request::input('leave_type_id');
        $calcDay = Helper::getDays($start_date, $end_date, $leave_type_id,$employee_id);
        $employee = Employee::select('id','unit_id','join_date')->where('id',$employee_id)->first();
       
        //if leave_type is carry_over leave make seperate calculation carry over leave id is 2
        if($leave_type_id != 2)
        {
            $remainingDays = Helper::getRemainingDays($leave_type_id,$employee);
        }else{
            $remainingDays = Helper::getRemainingCarryOverLeave($employee);
        }
        // dd($remainingDays);
        if(\Request::input('leave_time') != 'full'){
            $remainingDays = $remainingDays*2;
            $calcDay = $calcDay/2;
        }
        // return [$leave_type_id,$start_date,$end_date,$remainingDays,$calcDay];
        if($calcDay <= $remainingDays){
            return ['days'=>$calcDay];
        }else 
            return ['days'=>'0','reason'=>'Allowed leave days has been maxed out for selected leave type.'];     
    }


    private function nonEligibleFullLeaveDays($data,$employee_id)
    {   

        if($data['leave_time']=='full'){
            $not_eligible_dates = LeaveRequest::select('id','start_date','end_date','employee_id')
                                    ->where('employee_id',$employee_id)
                                    ->where(function($query) use($data){
                                        $query->where('start_date','>=',$data['start_date'])
                                                ->where('start_date','<=',$data['end_date']);
                                    })    
                                    ->orWhere(function($query) use($data){
                                        $query->where('end_date','>=',$data['start_date'])
                                                ->where('end_date','<=',$data['end_date']);
                                        })
                                    ->get();
        }else{
            if($data['leave_time']=='first'){
                $not_eligible_dates = LeaveRequest::select('id','start_date','end_date','employee_id','full_leave','half_leave')
                                    ->where('employee_id',$employee_id)
                                     ->where(function($query) use($data){
                                         $query->where('half_leave','first');
                                     })
                                    ->where(function($query) use($data){
                                        $query->where('start_date','>=',$data['start_date'])
                                                ->where('start_date','<=',$data['end_date']);
                                    })    
                                    ->orWhere(function($query) use($data){
                                        $query->where('full_leave','1')
                                                ->where('end_date','>=',$data['start_date'])
                                                ->where('end_date','<=',$data['end_date']);
                                        })
                                    ->get();
            }
            else if($data['leave_time']=='second'){
                $not_eligible_dates = LeaveRequest::select('id','start_date','end_date','employee_id','full_leave','half_leave')
                                    ->where('employee_id',$employee_id)
                                     ->where(function($query) use($data){
                                         $query->where('half_leave','second');
                                     })
                                    ->where(function($query) use($data){
                                        $query->where('start_date','>=',$data['start_date'])
                                                ->where('start_date','<=',$data['end_date']);
                                    })    
                                    ->orWhere(function($query) use($data){
                                        $query->where('full_leave','1')
                                                ->where('end_date','>=',$data['start_date'])
                                                ->where('end_date','<=',$data['end_date']);
                                        })
                                    ->get();
            }
        }
       return $not_eligible_dates;
    }
}
