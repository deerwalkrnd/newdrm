<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileCategoryRequest extends FormRequest
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
            'category_name'=>'required|string|max:255',
            'status'=>'required|string|max:8',

        ];
    }
}
