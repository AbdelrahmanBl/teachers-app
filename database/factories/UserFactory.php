<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker,$params) {
	$fakerAR = Factory::create('ar_SA');
	$type    = $params['type'];

	if($type == 'S') {
		return [
			'first_name'			=> $fakerAR->firstName,
			'last_name'        		=> "{$fakerAR->firstName} {$fakerAR->lastName}",
			'email' 		 		=> $faker->unique()->safeEmail,
			'password'       		=> App::make('hash')->make(123456),
			'mobile'                => "01{$faker->randomElement([2,0,5,1])}". rand(11111111,99999999),
			'parent_mobile1'        => "01{$faker->randomElement([5,2,1,0])}". rand(11111111,99999999),
			'type'           		=> $type,
			'year'           		=> $faker->randomElement([1,2,3]),
			'student_status'		=> 'WAITING',
			'created_at'            => Carbon::now()
		];
	}
	else if($type == 'T') {
		return [
			'package_id'   			=> $faker->randomElement([1,2,3]),
			'first_name'			=> $fakerAR->firstName,
			'last_name'        		=> "{$fakerAR->firstName} {$fakerAR->lastName}",
			'email' 		 		=> $faker->unique()->safeEmail,
			'password'       		=> App::make('hash')->make(123456),
			'mobile'                => "01{$faker->randomElement([2,0,5,1])}". rand(11111111,99999999),
			'type'           		=> $type,
			'is_rtl'				=> $faker->randomElement([1,0]),
			'students_number'		=> 0,
			'appointments_number'	=> 0,
			'exams_number'			=> 0,
			'accept_register'		=> 1,
			'created_at'            => Carbon::now()
		];
	}

    
});
