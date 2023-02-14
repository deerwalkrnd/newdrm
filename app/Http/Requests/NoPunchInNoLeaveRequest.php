<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoPunchInNoLeaveRequest extends FormRequest
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
            'employee_id'=>'required|integer|exists:employees,id|unique:no_punch_in_no_leaves,employee_id,'.$this->id.',id,date,'.$this->date,
            'date'=>'required|date',
        ];
    }
}
