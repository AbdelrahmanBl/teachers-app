<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempStudent extends Model
{
    protected $fillable = [
        'teacher_id','appointment_id','student_id'
        // ,'year','first_name','last_name','email','password','mobile','parent_mobile1','parent_mobile2','type','process'
    ];

    public function student()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
