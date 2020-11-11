<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\TempStudent;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TempStudent::class, function (Faker $faker) {
    return [
    	'teacher_id'			=> 1,
		'appointment_id'        => $faker->randomElement([13,12,8,7]),
    	'year'			        => $faker->randomElement([1,2,3]),
    	
    	'first_name'			=> $faker->firstName,
    	'last_name'        		=> $faker->lastName,
    	'email'          		=> $faker->unique()->safeEmail,
    	'password'              => App::make('hash')->make(123456),
    	'mobile'                => '01207897589',
    	'parent_mobile1'        => '01007183293',
    	'parent_mobile2'        => '01148953405',
    	'created_at'			=> Carbon::now()      
    ];
});
