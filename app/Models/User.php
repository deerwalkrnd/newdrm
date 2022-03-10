<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPassword as ResetPasswordNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'version',
        'account_expired',
        'account_locked',
        'department_id',
        'organization_id',
        'employee_id',
        'role_id',
        'enable',
        'password',
        'password_expired',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function getEmailForPasswordReset()
    {
        return $this->employee->email;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getFullnameAttribute()
    {
        return $this->attributes['fullname'] = ucfirst($this->employee->first_name) . ' ' . $this->employee->middle_name . ' ' . ucfirst($this->employee->last_name);
    }

    public function routeNotificationForMail($notification)
    { 
        return [$this->employee->email => $this->fullname];
    }
}
