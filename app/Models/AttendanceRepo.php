<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRepo extends Model
{
    protected $fillable = ["class_no","month","year","appointment_id","teacher_id"];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
