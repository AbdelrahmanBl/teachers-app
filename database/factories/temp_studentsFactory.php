<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\TempStudent;
use Carbon\Carbon;
use Faker\Generator as Faker;


$factory->define(TempStudent::class, function (Faker $faker,$params) {
	return [
		'teacher_id'			=> $params['teacher_id'],
		'appointment_id'        => $params['appointment_id'],
		'student_id'            => $params['student_id'],
		
		'created_at'			=> Carbon::now()      
	];
});
