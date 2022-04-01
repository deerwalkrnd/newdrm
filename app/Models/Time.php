<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    const MAX_PUNCH_IN = 'Punch in Maximum Time';
    const MAX_HALF_PUNCH_IN = 'First Half Leave Maximum Punch in Time';  
    const MIN_PUNCH_OUT = 'Punch Out Minimum Time';
    const MIN_HALF_PUNCH_OUT = 'Second Half Leave Minimum Punch Out Time';

    public $fillable = [
        'name',
        'time', 
    ];
}
