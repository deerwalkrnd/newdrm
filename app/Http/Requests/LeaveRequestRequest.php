<?php

namespace App\Http\Requests;

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
        $calcDay = ((strtotime($end_date) - strtotime($start_date))/ (60 * 60 * 24)) + 1;
        // dd($calcDay);
        return [
            'start_date' => 'required|date|after_or_equal:'.$today,
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|in:'.$calcDay,
            'leave_type_id' => 'required|numeric',
            'leave_time' => 'required|in:full,first,second',
            'reason' => 'required|string',
        ];
    }
}
