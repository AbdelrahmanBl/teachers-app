<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Attendance extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'attendances';

    protected $fillable = [];

    public $timestamps = false;
}
