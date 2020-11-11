<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
    	'name','desc','students_limit','appointment_limit','exams_limit','price'
    ];
}
