<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        'name',
        'gender',
        'paid_unpaid',
        'include_holiday'
    ];
}
