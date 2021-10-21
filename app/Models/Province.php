<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    public $fillable=[
        'province_name'
    ];
    public function district()
    {
        return $this->hasMany(District::class,'province_id');
    }
}
