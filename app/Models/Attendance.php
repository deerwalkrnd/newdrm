<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public $fillable = [
        'employee_id',
        'punch_in_time',
        'late_punch_in',
        'punch_out_time',
        'missed_punch_out',
        'reason'
    ];
}
