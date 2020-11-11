<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
    	'teacher_id','days','time_from','time_to','year'
    ];
    public $timestamps = false;
}
