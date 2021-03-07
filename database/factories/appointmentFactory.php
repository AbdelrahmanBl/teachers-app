<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Appointment;
use App\Models\Day;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker,$params) {
    
    $days     = Day::get();
    $day      = $days->random(1)->first();
    
    $duration = ($day->id == 4)? 3 : 1.5;
    $time = time() + rand(1111,9999);
    $time_from = date('H:i', $time );
    $time_to   = date('H:i', $time + ($duration*60*60) );

    return [
        'teacher_id'            => $params['teacher_id'],
        'days_id'               => $day->id,
        'time_from'             => $time_from,
        'time_to'               => $time_to,
        'year'                  => $params['year'],
        'max_class_no'          => ($day->id == 4)? 3 : 8,
    ];
});
