<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class PaymentRepo extends Model
{
    protected $fillable = ["name","desc","teacher_id","year","appointment_ids"];

    public function getAppointmentsArrAttribute()
    {
        $all = json_decode($this->appointment_ids);
        // $gt_appointment_ids = request()->appointment_ids;
        // if($gt_appointment_ids) {
        //     $appointment_ids = explode(',',$gt_appointment_ids);
            
        //     $appointment_ids = collect($appointment_ids)->transform(function($id) {
        //         return (int)$id;
        //     })->toArray();
            
        //     $valid_ids = [];
        //     foreach($appointment_ids as $appointment_id) {
        //         if(array_search($appointment_id,$all) !== false)
        //             $valid_ids[] = $appointment_id;
        //     }
        //     $all = $valid_ids;
        // }
        return $all;
    }

    public function getAppointmentsAttribute()
    {
        $appointments = Appointment::whereIn('id',$this->appointmentsArr)->get();
        $appointments->transform(function($appointment) {
            $map['id']        = $appointment->id;
            $map['days']      = $appointment->day->day;
            $map['time_from'] = $appointment->time_from;

            return $map;
        });
        return $appointments;
    }
}
