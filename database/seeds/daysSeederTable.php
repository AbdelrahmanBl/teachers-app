<?php

use Illuminate\Database\Seeder;
use App\Models\Day;

class daysSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Day::truncate();
        Day::insert([
            [
                'day'   => 'السبت و الثلاثاء',
            ],
            [
                'day'   => 'الاحد و الاربعاء',
            ],
            [
                'day'   => 'الاثنين و الخميس',
            ],
            [
                'day'   => 'السبت دبل',
            ]
        ]);
    }
}
