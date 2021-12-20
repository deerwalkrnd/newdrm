<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    use HasFactory;

    public $fillable = [
        'category_name',
        'status'
    ];
    
    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class,'file_category_id');
    }
}
