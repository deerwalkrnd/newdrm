<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public $fillable=[
        'name',
        'unit_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class,'department_id');
    }
}
