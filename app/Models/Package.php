<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
    	'name','desc','students_limit','appointment_limit','exams_limit','price'
    ];

    public function getGetImageAttribute()
    {
      $file = "storage/packages/{$this->image}";
      return file_exists(public_path($file)) && $this->image ? asset($file) : asset("default.jpg") ; 
    }
}
