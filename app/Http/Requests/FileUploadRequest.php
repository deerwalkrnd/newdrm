<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'file' => 'file|required|mimes:jpg,png,jpeg,pdf',
            'file_category_id'=>'required|integer|exists:file_categories,id',
            'employee_id'=>'required|integer',
        ];
    }
}
