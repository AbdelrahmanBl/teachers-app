<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(settingsSeeder::class);
        // $this->call(daysSeederTable::class);
        // $this->call(packagesSeederTable::class);
        // $this->call(exam_requestsSeederTable::class);
        // $this->call(temp_studentsSeeder::class);
        $this->call(userSeeder::class);
        $this->call(subscribtionSeeder::class);
    }
}
