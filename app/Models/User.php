<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
 	protected $fillable = [
 		/*'appointment_id',*/'package_id','year','first_name','last_name','email','password','mobile','parent_mobile1','parent_mobile2','type','students_number','appointments_number','exams_number','accept_register','is_rtl','accept_register'
    ];
    protected $hidden = [
        'password'
      ]; 
      
    public function getFullnameAttribute()
    {
      return "{$this->first_name} {$this->last_name}";
    }

    public function getGetImageAttribute()
    {
      $file = "storage/profiles/{$this->image}";
      return file_exists(public_path($file)) && $this->image ? asset($file) : asset("default.jpg") ; 
    }
}
