<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailControl extends Model
{
    use HasFactory;

    public $fillable =[
        'name',
        'send_mail',
    ];
}
