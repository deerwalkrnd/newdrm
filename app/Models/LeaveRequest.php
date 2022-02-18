<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    public $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'days',
        'year',
        'leave_type_id',
        'full_leave',
        'half_leave',
        'reason',
        'acceptance',
        'requested_by',
        'accepted_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class,'leave_type_id');
    }

    public function accepted_by_detail()
    {
        return $this->hasOne(Employee::class,'id','accepted_by');
    }

    public function requested_by_detail()
    {
        return $this->hasOne(Employee::class,'id','requested_by');
    }
}
