<?php

use Illuminate\Database\Seeder;
use App\Models\Subscrption;

class subscribtionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subscrption::truncate();
        factory( Subscrption::class , 1000 )->create();
    }
}
