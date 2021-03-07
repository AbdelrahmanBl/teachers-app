<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;

class teacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = 'T';
        User::where('type',$type)->delete();
        factory( User::class , (int)Setting::where('key','teachers_no')->first()->value )->create(['type' => $type]);
    }
}
