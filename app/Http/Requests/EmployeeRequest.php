<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'employee_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => '',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required',
            'gender' => 'required',
            'father_name' => '',
            'mother_name' => '',
            'grand_father' => '',
            'mobile' => 'required',
            'alternative_mobile' => '',
            'home_phone' => '',
            'image' => '',
            'alter_email' => 'required',
            'cv' => '',
            'country' => '',
            'nationality' => '',
            'profile' => '',
            'blood_group' => '',
            'permanent_address' => 'required',
            'permanent_district' => 'required',
            'permanent_municipality' => 'required',
            'permanent_ward_no' => 'required',
            'permanent_tole' => 'required',
            'permanent_toletemp_add_same_as_per_add' => 'required',
            'temporary_address' => '',
            'temporary_district' => '',
            'temporary_municipality' => '',
            'temporary_ward_no' => '',
            'temporary_tole' => '',
            'join_date' => 'nullable|date',
            'intern_trainee_ship_date' => 'nullable|date',
            'manager_id' => '',
            'designation_id' => 'required',
            'designation_change_date' => 'nullable|date',
            'organization_id' => 'required',
            'unit_id'  => 'required',
            'email' => 'required',
            'emp_shift' => 'required',
            'remarks' => '',
        ];
    }
}
