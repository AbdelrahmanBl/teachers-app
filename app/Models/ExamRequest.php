<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRequest extends Model
{
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subscrption()
    {
        return $this->belongsTo(Subscrption::class);
    }
}
