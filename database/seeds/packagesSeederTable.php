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
				'desc'              => 'تمكنك من إضافة 300 طالب و 50 موعد و 500 امتحان',
                'students_limit'    => 300,
				'appointment_limit' => 50,
                'exams_limit'       => 500,
				'price'             => 1000,
            ],
            [
                'name'              => 'خطة الاسعار الكبرى',
                'desc'              => 'تمكنك من إضافة 600 طالب و 100 موعد و 1000 امتحان',
                'students_limit'    => 600,
				'appointment_limit' => 100,
                'exams_limit'       => 1000,
				'price'             => 2000,
            ]
        ]);
    }
}
