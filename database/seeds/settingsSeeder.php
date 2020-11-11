<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;
class settingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();
        Setting::insert([
            [
                'key'   => 'HEADER_KEY',
                'value' => 'ostazy'
            ],
            [
                'key'   => 'OATH_KEY',
                'value' => '123456'
            ],
            [
                'key'   => 'questions_limit',
                'value' => 100
            ],
            [
                'key'   => 'EXCEPTION',
                'value' => 1
            ]
        ]);
    }
}
