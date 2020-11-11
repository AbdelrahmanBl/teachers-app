<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Carbon\Carbon;
use App\Models\ExamRequest;
use App\Models\User;
use Faker\Generator as Faker;


$factory->define(ExamRequest::class, function (Faker $faker) {
    static $counter = 0;
    $users = User::where('type','S')->get();
    $status_arr = ['WAITING','DICONNECTED','COMPLETED','IN_EXAM'];
    $status = $faker->randomElement($status_arr);
    return [
    	'exam_id'            	=> 1,
    	'student_id'			=> $faker->randomElement([7,8,9]),//$users[$counter++]->id,
    	'teacher_id'			=> 1,
    	'status'				=> $status,
    	'duration_solve'        => ($status == 'COMPLETED')? rand(0,10) : NULL ,
    	'total_degree'          => ($status == 'COMPLETED')? rand(1,15) : NULL ,
    	'start_at'              => ($status == 'COMPLETED' || $status == 'IN_EXAM')? Carbon::now() : NULL ,
    	'created_at'			=> Carbon::now()      
    ];
});
