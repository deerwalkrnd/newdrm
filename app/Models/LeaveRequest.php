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
        'leave_type_id',
        'full_leave',
        'half_leave',
        'reason',
        'acceptance',
        'accepted_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class,'id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class,'id');
    }

    public function accepted_by_detail()
    {
        return $this->hasOne(Employee::class);
    }
}
