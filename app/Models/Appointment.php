<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
    	'teacher_id','days_id','time_from','time_to','year','max_class_no'
    ];
    public $timestamps = false;

    public function day()
    {
        return $this->belongsTo(Day::class,'days_id','id');
    }
}
