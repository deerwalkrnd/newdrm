<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'time_required'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class,'shift_id','id');
    }
}
