<?php

namespace App\Http\Requests;
use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
        $calcDay = Helper::getDays($start_date, $end_date, $leave_type_id);
        $start_date_year = date('Y',strtotime($start_date));
        return [
            'start_date' => 'required|date|after_or_equal:'.$today,
            'end_date' => 'required|date|after_or_equal:start_date|starts_with:'.$start_date_year,
            'days' => 'required|integer|in:'.$calcDay,
            'leave_type_id' => 'required|integer',
            'leave_time' => 'required|in:full,first,second',
            'reason' => 'required|string',
        ];
    }
}
