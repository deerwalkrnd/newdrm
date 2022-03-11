<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

use App\Models\Holiday;

class HolidayUniqueDate implements Rule,  DataAwareRule
{
    protected $data = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $holidays = Holiday::select('id','unit_id','date')
                            ->where('unit_id',$this->data['unit_id'])
                            ->whereDate('date',$this->data['date'])
                            ->where('id','!=',\Request::route('id'))
                            ->count();

        return $holidays === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The date must be unique.';
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
