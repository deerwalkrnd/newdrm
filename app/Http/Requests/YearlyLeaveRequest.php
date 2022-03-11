<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueYearlyLeaveType;

class YearlyLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->role->authority == 'hr';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'days'=>'required|integer',
            'unit_id'=>'nullable|exists:units,id',
            'status'=>'required|string',
            'year'=>'required|integer',
            'leave_type_id'=>['required','exists:leave_types,id',new UniqueYearlyLeaveType]
        ];
    }
}
