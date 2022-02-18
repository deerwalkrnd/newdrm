<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NoPunchInNoLeave;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Attendance;
class NoPunchInNoLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $date = date('Y-m-d');

        $weekDay = strtoupper(date('D',strtotime($date)));
        //all unit all holiday
        $isHoliday = Holiday::whereDate('date',$date)->where('unit_id',null)->where('female_only','0')->get()->count();

        $employees = Employee::select('id','first_name','last_name','middle_name','unit_id','gender','contract_status','manager_id','unit_id')
                        ->with('manager:id,first_name,last_name,middle_name')
                        ->where('contract_status','active')
                        ->whereDoesntHave('attendances', function ($query) use ($date) {
                            $query->whereDate('created_at', $date);
                        })
                        ->whereDoesntHave('leaveRequest', function($query) use ($date){
                            $query->whereDate('start_date','<=',$date)
                                    ->whereDate('end_date','>=',$date);
                        });

        if($weekDay == 'SAT' || $weekDay == 'SUN' || $isHoliday){
            $employees = Employee::where('id','-1');
        }else{
            //any holiday at all
            $anyHoliday = Holiday::whereDate('date',$date)->get()->count();
            if($anyHoliday){ 
                $all_unit_female_holiday = Holiday::whereDate('date',$date)->where('unit_id',null)->where('female_only','1')->get()->count();
                if($all_unit_female_holiday){
                    $employees = $employees->where('gender','!=','female');
                }
                $some_unit_holiday = Holiday::whereDate('date',$date)->where('unit_id','!=',null)->get()->count();
                if($some_unit_holiday){
                    $some_unit_all_holiday = Holiday::whereDate('date',$date)->where('unit_id','!=',null)->where('female_only','0')->get()->count();
                    if($some_unit_all_holiday){
                        $units = Holiday::select('unit_id')->whereDate('date',$date)->where('unit_id','!=',null)->where('female_only','0')->get()->toArray();
                        $employees = $employees->whereNotIn('unit_id',$units);
                    }
                    $some_unit_female_holiday = Holiday::whereDate('date',$date)->where('unit_id','!=',null)->where('female_only','1')->get()->count();
                    if ($some_unit_female_holiday){
                        $units = Holiday::select('unit_id')->whereDate('date',$date)->where('unit_id','!=',null)->where('female_only','1')->get()->toArray();
                        $employees = $employees->whereNotIn('unit_id',$units)->where('gender','!=','female');
                    }
                }              
            }
        }
        // dd($units);
        $employees = $employees->get();
        foreach($employees as $employee){
            NoPunchInNoLeave::create([
                'employee_id'=>$employee->id,
                'date'=>$date,
            ]);
        }
        return true;
    }


    public function remove(){
        // dd("jer");
        $attendances = Attendance::select('id','employee_id','reason','punch_in_time')->whereDate('created_at',date('Y-m-d'))->where('reason','Forced Punch In')->get();
        foreach($attendances as $attendance){
            $noPunchInNoLeave = NoPunchInNoLeave::where('employee_id',$attendance->employee_id)->whereDate('date',$attendance->punch_in_time)->first();
            // if($noPunchInNoLeave)
            $noPunchInNoLeave->delete();
        }
        return true;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
