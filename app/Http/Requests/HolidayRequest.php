<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HolidayUniqueDate;

class HolidayRequest extends FormRequest
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
        // dd(\Request::input());
        return [
            'unit_id'=>'nullable|exists:units,id',
            'name'=>'required|string|max:255',
            'date' => ['required','date', new HolidayUniqueDate], 
            'female_only'=>'required|integer',
            'festival_only'=>'nullable',
            'image'=>'nullable'
        ];
    }
}