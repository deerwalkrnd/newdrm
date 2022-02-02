<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

use App\Models\YearlyLeave;

class UniqueYearlyLeaveType implements Rule, DataAwareRule
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
        $yearlyLeave = YearlyLeave::select('id','unit_id','year','leave_type_id')
                            ->where('unit_id',$this->data['unit_id'])
                            ->where('year',$this->data['year'])
                            ->where('id','!=',\Request::route('id'))
                            ->where('leave_type_id',$this->data['leave_type_id'])
                            ->count();

        return $yearlyLeave === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Yearly Leave Type Must Be Unique';
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
