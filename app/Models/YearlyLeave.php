<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyLeave extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        'unit_id',
        'leave_type_id',
        'days',
        'status',
        'year'
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function leaveType()
    {
        return $this->belongsTo(leaveType::class);
    }
}
