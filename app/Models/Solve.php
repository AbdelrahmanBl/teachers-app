<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Solve extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'solves';

    protected $fillable = [
    	'student_id','exam_id','question_id','respond','images','degree'
    ];

    public $timestamps = false;

    public function getGetImagesAttribute()
    {
      $images = NULL;
      foreach($this->images as $image) {
        $file = "storage/solves/{$image}";
        $images[] = asset($file) ; 
      }
      return $images;
    }
}

