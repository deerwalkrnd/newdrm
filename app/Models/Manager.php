<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;

    public $fillable=[
        'version',
        'employee_id',
        'is_active'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
