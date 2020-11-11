<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('id','!=',1)->delete();
        factory( User::class , 1000 )->create();
    }
}
