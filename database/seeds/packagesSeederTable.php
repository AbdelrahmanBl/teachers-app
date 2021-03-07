<?php

use Illuminate\Database\Seeder;
use App\Models\Package;

class packagesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::truncate();
        Package::insert([
            [
				'name'              => 'خطة الاسعار الصغرى',
				'desc'              => 'يتم دفع 30 ج رسوم الطالب',
                'students_limit'    => 300,
				'appointment_limit' => 50,
                'exams_limit'       => 500,
				'price'             => 9000,
            ],
            [
				'name'              => 'خطة الاسعار الوسطي',
				'desc'              => 'يتم دفع 30 ج رسوم الطالب',
                'students_limit'    => 600,
				'appointment_limit' => 100,
                'exams_limit'       => 1000,
				'price'             => 18000,
            ],
            [
                'name'              => 'خطة الاسعار الكبرى',
                'desc'              => 'يتم دفع 30 ج رسوم الطالب',
                'students_limit'    => 900,
				'appointment_limit' => 150,
                'exams_limit'       => 1500,
				'price'             => 27000,
            ]
        ]);
    }
}
