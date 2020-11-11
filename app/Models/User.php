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
}
