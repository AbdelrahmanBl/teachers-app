<?php

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Setting;
use App\Models\User;

class appointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Appointment::truncate();
        $teachers = User::where('type','T')->get();
        $years    = [1,2,3];
        
        $appointments_rounds = (int)Setting::where('key','appointments_rounds')->first()->value;
        for($i = 1 ; $i <= $appointments_rounds ; $i++) {
            foreach($teachers as $teacher) {
                foreach($years as $year) {
                    $appointment = factory( Appointment::class )->make(['teacher_id' => $teacher->id , 'year' => $year])->makeHidden([]);
                    $appointment->save();
                }
            }
        }
    }
}
