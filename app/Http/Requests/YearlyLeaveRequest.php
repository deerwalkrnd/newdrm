<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class YearlyLeaveRequest extends FormRequest
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
        return [
            'days'=>'required|integer',
            'unit_id'=>'required|exists:units,id',
            'leave_type_id'=>'required|exists:leave_types,id',
            'status'=>'required|string',
            'year'=>'required|integer'
        ];
    }
}
