<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ExamRequest;

class handleInExam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:inExam';

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
        try{
        $where  = array(
            'status'   => 'IN_EXAM',
        );
        $exam_requests = ExamRequest::where($where)->get();
        $exam_requests_arr = array();
        $users_arr         = array();
 
        $delay_time = 10; // In minutes
        $now    = date('Y-m-d H:i:s');
        foreach($exam_requests as $exam_request){
            $layout = round((strtotime($now) - strtotime(  $exam_request->end_at  )) / 60,2);
            if($layout < $delay_time)
                continue;
            $exam_requests_arr[]  = $exam_request->id;
            $users_arr[]          = $exam_request->student_id;
        }
        if(count($exam_requests_arr) > 0 && count($exam_requests_arr) == count($users_arr) ){
        ExamRequest::whereIn('id',$exam_requests_arr)->update(['status' => 'DICONNECTED']);
        User::whereIn('id',$users_arr)->update(['student_status' => 'WAITING']);
        }
        $this->info('Done !');
    }catch(Exception $e){
        $this->info($e->getMessage());
        return 0;
    }
    }
}
