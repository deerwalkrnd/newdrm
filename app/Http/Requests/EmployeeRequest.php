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
            // 'employee_id' => 'required|unique:employees,employee_id,'.$this->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable|max:255',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required|max:255',
            'gender' => 'required|max:255',
            'father_name' => 'nullable|max:255',
            'mother_name' => 'nullable|max:255',
            'grand_father' => 'nullable|max:255',
            'mobile' => 'required|digits:10',
            'alternative_mobile' => 'nullable|digits:10',
            'home_phone' => 'nullable|digits:7',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'alter_email' => 'required|email|max:255',
            'cv' => 'nullable|mimes:pdf',
            'country' => 'required|max:255',
            'nationality' => 'nullable|max:255',
            'profile' => 'nullable|max:255',
            'blood_group' => 'nullable|max:5',
            'permanent_address' => 'required|max:255',
            'permanent_district' => 'required|max:255',
            'permanent_municipality' => 'required|max:255',
            'permanent_ward_no' => 'required|max:2',
            'permanent_tole' => 'required|max:255',
            'permanent_toletemp_add_same_as_per_add' => 'required|max:255',
            'temporary_address' => 'nullable|max:255',
            'temporary_district' => 'nullable|max:255',
            'temporary_municipality' => 'nullable|max:255',
            'temporary_ward_no' => 'nullable|max:2',
            'temporary_tole' => 'nullable|max:255',
            'join_date' => 'nullable|date',
            'intern_trainee_ship_date' => 'nullable|date',
            'service_type' => 'required|exists:service_types,id',
            'manager_id' => 'nullable|exists:managers,id',
            'designation_id' => 'required|max:255',
            'designation_change_date' => 'nullable|date',
            'organization_id' => 'required|max:255',
            'unit_id'  => 'required|max:255',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'role' => 'required|exists:roles,id',
            'emp_shift' => 'required|max:255',
            'remarks' => 'nullable|max:255',
        ];
    }
}
