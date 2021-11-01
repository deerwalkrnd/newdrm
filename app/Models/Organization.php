<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        'name',
        'code',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class,'organization_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class,'organization_id');
    }
}
