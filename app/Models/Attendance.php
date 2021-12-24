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
        'punch_in_ip',
        'late_punch_in',
        'punch_out_time',
        'punch_out_ip',
        'missed_punch_out',
        'reason'
    ];
}
