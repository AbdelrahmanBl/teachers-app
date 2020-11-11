<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Message extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'messages';

    protected $fillable = [
    	'teacher_id','type','target','message','created_at'
    ];

    public $timestamps = false;
}


