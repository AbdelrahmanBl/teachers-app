<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Subscrption;


class handlePaymentRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Every Day
        try {
        $appointments  = Appointment::get();
        $update_ids    = [];  // IDs Of Appointments
        $notifications = []; 
        foreach($appointments as $appointment) {
            if($appointment->current_class_no == $appointment->max_class_no) {
                $update_ids[] = (int)$appointment->id;
            }
        }
        $subscrptions = Subscrption::whereIn('appointment_id',$update_ids)->get();
        foreach($subscrptions as $subscrption) {
            $notify = new Notification();
            $notify->sender_id    = (int)$subscrption->teacher_id;
            $notify->reciever_id  = (int)$subscrption->student_id;
            $notify->event        = 'PR';
            $notify->is_seen      = 0;
            $notify->seen_at      = NULL;
            $notify->created_at   = date('Y-m-d H:i:s');
            $notifications[] = $notify->toArray();
        }
        
        if(count($update_ids) > 0)
            Appointment::whereIn('id',$update_ids)->update(['current_class_no' => 0]);
        if(count($notifications) > 0)
            Notification::insert($notifications);

        $this->info('Done !');
        }catch(Exception $e){
            $this->info($e->getMessage());
            return 0;
        }
    }
}
