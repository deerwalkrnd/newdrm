<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class SubordinateLeaveRequestRequest extends FormRequest
{
    public function authorize()
    {
        if(Auth::user()->role->authority == 'hr' || Auth::user()->role->authority == 'manager')
            return true;
        else
            return false;
    }

    public function rules()
    {
        if(array_key_exists('employee_id', Request::input()) == false)
            return ['employee_id' => 'required|exists:employees,id'];
        
        $today = date('Y-m-d');

        if(Route::current()->getActionName() == "App\Http\Controllers\LeaveRequestController@update")
            $today = date("Y-m-d", strtotime('-1 month', strtotime($today)));

        $start_date    = Request::input('start_date');
        $end_date      = Request::input('end_date');
        $leave_type_id = Request::input('leave_type_id');
        $employee_id   = Request::input('employee_id');

        $calcDay = Helper::getDays($start_date, $end_date, $leave_type_id, $employee_id);
        if(Request::input('leave_time') != 'full'){
            $calcDay = $calcDay / 2;
        }

        // Balance validation for carry-over types (15=Personal, 16=Sick)
        $employee = Employee::select('id', 'unit_id', 'join_date')->where('id', $employee_id)->first();
        if($leave_type_id == 15 || $leave_type_id == 16) {
            $remainingDays = Helper::getRemainingCarryOverLeave($employee, $leave_type_id);
        } else {
            $remainingDays = Helper::getRemainingDays($leave_type_id, $employee);
        }

        if($leave_type_id == 9){
            $remainingDays = 100;
        }

        return [
            'employee_id'  => 'required|exists:employees,id',
            'start_date'   => 'required|date|after_or_equal:'.$today,
            'end_date'     => 'required|date|after_or_equal:start_date',
            'days'         => 'required|in:'.$calcDay.'|between:0,'.$remainingDays,
            'leave_type_id'=> 'required|integer',
            'leave_time'   => 'required|in:full,first,second',
            'reason'       => 'required|string',
        ];
    }
}
