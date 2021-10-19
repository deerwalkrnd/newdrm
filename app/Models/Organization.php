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

    public function unit()
    {
        return $this->hasOne(Organization::class,'organization_id');
    }
}
