<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    const Max_Punch_In_Time = 'Punch in Maximum Time';
    const First_Half_Punch_In_Time = 'First Half Leave Maximum Punch in Time';
    const Min_Punch_Out_Time = 'Punch Out Minimum Time';
    const Second_Half_Punch_Out_Time = 'Second Half Leave Minimum Punch Out Time';

    public $fillable = [
        'name',
        'time', 
    ];
}
