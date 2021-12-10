<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;
     public $fillable=[
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'relationship',
        'phone_no',
        'alternate_phone_no'
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

}
