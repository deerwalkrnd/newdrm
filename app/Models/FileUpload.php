<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

      public $fillable = [
        'file_category_id',
        'file_name',
        'uploaded_by',
        'employee_id'
    ];

    public function fileCategory()
    {
        return $this->belongsTo(FileCategory::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
