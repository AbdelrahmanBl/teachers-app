<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Payment extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'payments';

    protected $fillable = [];

    public $timestamps = false;
}
