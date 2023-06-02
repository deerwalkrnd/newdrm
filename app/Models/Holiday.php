<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    public $fillable = [
        'unit_id',
        'name',
        'date',
        'female_only',
        'festival_only',
        'image'
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}