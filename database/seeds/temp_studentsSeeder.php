<?php

use Illuminate\Database\Seeder;
use App\Models\TempStudent;
use App\Models\User;
use App\Models\Appointment;

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
        $teachers = User::where('type','T')->get();
        $students = User::where('type','S')->get();
        $appointments = Appointment::get();
        
        foreach($teachers as $teacher) {
            foreach($students as $student) {
                $appointment = $appointments->where('teacher_id',$teacher->id)->where('year',$student->year)->random(1)->first();
                
                $insert = factory( TempStudent::class )->make(['teacher_id' => $teacher->id , 'appointment_id' => $appointment->id, 'student_id' => $student->id]);
                $insert->save();
            }
        }
    }
}
