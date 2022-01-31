<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        'unit_name',
        'organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class,'unit_id');
    }
    public function yearlyLeaves()
    {
        return $this->hasMany(YearlyLeave::class,'unit_id');
    }
    public function holidays()
    {
        return $this->hasMany(Holiday::class,'unit_id');
    }
    public function topEmployees()
    {
        return $this->hasMany(Employee::class,'unit_id')->select('id','first_name','middle_name','last_name','manager_id')->where('manager_id',null);
    }
}
