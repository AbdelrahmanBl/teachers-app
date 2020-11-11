<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Notification extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
    	'sender_id','reciever_id','message','event','is_seen','seen_at','created_at'
    ];

    public $timestamps = false;
}
