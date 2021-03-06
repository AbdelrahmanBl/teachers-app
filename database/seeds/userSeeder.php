<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = 'S';
        User::where('type',$type)->delete();
        factory( User::class , (int)Setting::where('key','students_no')->first()->value )->create(['type' => $type]);
    }
}
