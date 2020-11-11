<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
    	'teacher_id','exam_name','desc','degree','question_no','duration','is_hide','year','status','is_published','is_rtl'
    ];
}
