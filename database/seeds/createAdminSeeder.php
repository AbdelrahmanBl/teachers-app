<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class createAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('type','A')->delete();
        User::create([
            'first_name'    => 'Abdelrahman',
            'last_name'     => 'Gamal',
            'email'         => 'admin@topstudents.com',
            'password'      => App::make('hash')->make(123456),
            'type'          => 'A',
        ]);
    }
}
