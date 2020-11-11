<?php

use Illuminate\Database\Seeder;
use App\Models\TempStudent;

class temp_studentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TempStudent::truncate();
        factory( TempStudent::class , 500 )->create();
    }
}
