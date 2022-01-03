<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        // 'employee_id',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'marital_status',
        'gender',
        'father_name',
        'mother_name',
        'grand_father',
        'mobile',
        'alternative_mobile',
        'home_phone',
        'image_name',
        'alter_email',
        'cv_file_name',
        'country',
        'nationality',
        'profile',
        'blood_group',
        'permanent_address',
        'permanent_district',
        'permanent_municipality',
        'permanent_ward_no',
        'permanent_tole',
        'temp_add_same_as_per_add',
        'temporary_address',
        'temporary_district',
        'temporary_municipality',
        'temporary_ward_no',
        'temporary_tole',
        'join_date',
        'intern_trainee_ship_date',
        'service_type',
        'manager_id',
        'designation_id',
        'designation_change_date',
        'organization_id',
        'unit_id',
        'email',
        'shift_id',
        'remarks',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class,'service_type','id');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    // public function managers()
    // {
    //     return $this->hasMany(Manager::class,'manager_id');
    // }

    public function manager(){
        return $this->hasOne(Employee::class,'id','manager_id');
    }
    // public function manager()
    // {
    //     return $this->belongsTo(Manager::class,'manager_id');
    // }
    public function designation()
    {
        return $this->hasOne(Designation::class,'id','designation_id');
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class,'employee_id','id');
    }
    public function user(){
        return $this->hasOne(User::class,'employee_id','id');
    }
    public function fileUploads(){
        return $this->hasMany(FileUpload::class,'employee_id','id');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class,'employee_id','id')->orderBy('punch_in_time');
    }
    public function province(){
        return $this->hasOne(Province::class,'id','permanent_address');
    }
    public function district(){
        return $this->hasOne(District::class,'id','permanent_district');
    }
    public function shift(){
        return $this->hasOne(Shift::class,'id','shift_id');
    }

}
