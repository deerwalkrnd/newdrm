<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    public $fillable=[
        'district_name',
        'province_id'
    ];

    public function province(){
        return $this->belongsToMany(Province::class);
    }
    public function employees(){
        return $this->hasMany(Employee::class,'permanent_district','id');
    }
}
