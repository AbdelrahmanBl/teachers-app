<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Question extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'questions';

    protected $fillable = [
    	'exam_id','question_type','main_question','question','true_respond','responds','outside_counter','inside_counter','degree','image'
    ];

    public $timestamps = false;
}

