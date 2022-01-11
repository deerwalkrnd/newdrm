<?php

namespace App\Http\Requests;
use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class SubordinateLeaveRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(\Auth::user()->role->authority == 'hr' || \Auth::user()->role->authority == 'manager')
            return true;
        else
            return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today = date('Y-m-d');
        $start_date = \Request::input('start_date');
        $end_date = \Request::input('end_date');
        $leave_type_id = \Request::input('leave_type_id');
        $calcDay = Helper::getDays($start_date, $end_date,$leave_type_id);
        // dd($start_date,$end_date,$calcDay);
        return [
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date|after_or_equal:'.$today,
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|integer|in:'.$calcDay,
            'leave_type_id' => 'required|integer',
            'leave_time' => 'required|in:full,first,second',
            'reason' => 'required|string',
        ];
    }
}
