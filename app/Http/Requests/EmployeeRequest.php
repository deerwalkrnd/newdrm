<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

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
            // 'employee_id' => 'required',
            // 'employee_id' => 'required|unique:employees,employee_id,'.$this->id,
            'first_name' => 'required|max:255|alpha',
            'last_name' => 'required|max:255|alpha',
            'middle_name' => 'nullable|max:255|alpha',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required|max:255',
            'gender' => 'required|max:255|alpha',
            'father_name' => 'nullable|max:255|alpha',
            'mother_name' => 'nullable|max:255|alpha',
            'grand_father' => 'nullable|max:255|alpha',
            'mobile' => 'required|digits:10',
            'alternative_mobile' => 'nullable|digits:10',
            'home_phone' => 'nullable|digits:7',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'alter_email' => 'required|email|max:255',
            'cv' => 'nullable|mimes:pdf',
            'country' => 'required|max:255|alpha',
            'nationality' => 'nullable|max:255|alpha',
            'profile' => 'nullable|max:255|string',
            'blood_group' => 'nullable|max:5|string',
            'permanent_address' => 'required|max:255|integer',
            'permanent_district' => 'required|max:255|integer',
            'permanent_municipality' => 'required|max:255|string',
            'permanent_ward_no' => 'required|max:2|integer',
            'permanent_tole' => 'required|max:255|string',
            'permanent_toletemp_add_same_as_per_add' => 'required|max:255|integer',
            'temporary_address' => 'nullable|max:255|string',
            'temporary_district' => 'nullable|max:255|string',
            'temporary_municipality' => 'nullable|max:255|string',
            'temporary_ward_no' => 'nullable|max:50|integer',
            'temporary_tole' => 'nullable|max:255|string',
            'join_date' => 'nullable|date',
            'intern_trainee_ship_date' => 'nullable|date',
            'service_type' => 'required|exists:service_types,id',
            'manager_id' => 'nullable|exists:managers,id',
            'designation_id' => 'required|max:255|integer',
            'designation_change_date' => 'nullable|date',
            'organization_id' => 'required|max:255|integer',
            'unit_id'  => 'required|max:255|integer',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'role' => 'required|exists:roles,id',
            'shift_id' => 'required|max:255|integer',
            'remarks' => 'nullable|max:255|string',
            'emg_first_name' =>'required|max:255|string',
            'emg_middle_name' =>'nullable|max:255|string',
            'emg_last_name' =>'required|max:255|string',
            'emg_relationship' =>'required|max:255|string',
            'emg_contact' =>'required|digits_between:7,15',
            'emg_alternate_contact' =>'nullable||digits_between:7,15',
        ];
    }
}
