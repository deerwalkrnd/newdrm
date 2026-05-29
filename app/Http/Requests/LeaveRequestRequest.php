<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Helpers\NepaliCalendarHelper;

class LeaveRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(Request::input('employee_id')!=null)
            $employee_id = Request::input('employee_id');
        else
            $employee_id = Auth::user()->employee_id;
            
        $today = date('Y-m-d');
        $start_date = Request::input('start_date');
        $end_date = Request::input('end_date');
        $leave_type_id = Request::input('leave_type_id');
        $calcDay = Helper::getDays($start_date, $end_date, $leave_type_id, $employee_id);
        $employee = Employee::select('id','unit_id','join_date')->where('id', $employee_id)->first();

        // leave_type_id 15 = Carry Over - Personal, 16 = Carry Over - Sick
        if($leave_type_id == 15 || $leave_type_id == 16)
        {
            $remainingDays = Helper::getRemainingCarryOverLeave($employee, $leave_type_id);
        }else{
            $remainingDays = Helper::getRemainingDays($leave_type_id,$employee);
        }
        if(Request::input('leave_time') != 'full'){
            $calcDay = $calcDay/2;
        }
        if ($leave_type_id == 9){
            $remainingDays = 100;
        }
        // dd(Request::input('days'));
        
        // dd('applied leave days: ',$calcDay,'remaining leave days: ',$remainingDays,'days',Request::input('days'));
        return [
            'start_date' => 'required|date|after_or_equal:'.$today,
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|in:'.$calcDay.'|between:0,'.$remainingDays,
            'leave_type_id' => 'required|integer',
            'leave_time' => 'required|in:full,first,second',
            'reason' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'max' => 'Leave Limit Exceeded',
        ];
    }
}
