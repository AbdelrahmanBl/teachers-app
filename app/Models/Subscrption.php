<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscrption extends Model
{
    // PE     >>>  Publish Exam
    // ME     >>>  Mark Exam
    // ASR    >>>  Subscriptions
    // PR    >>>   Request Pay Month
    // PDR   >>>   Request Paid Month

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
