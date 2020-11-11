<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Subscrption;
use Faker\Generator as Faker;
use App\Models\User;

$factory->define(Subscrption::class, function (Faker $faker) {
	static $counter = 0;
    $users = User::where('type','S')->get();
    return [
        'teacher_id'  	    => 1,
        'student_id'		=> $users[$counter++]->id,
        'appointment_id'	=> $faker->randomElement([21,20]),
        'type'				=> 'register',
    ];
});
