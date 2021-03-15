<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class removeNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:notifications';

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
        $now = date('Y-m-d H:i:s');
        $notifications = Notification::whereIn('event',['PE','ME','RS'])->where('created_at','<',$now)->get();
        $deleted = [];
        foreach($notifications as $notification) {
            $from   = strtotime(explode(' ',$notification->created_at)[0]);
            $to     = strtotime(explode(' ',$now)[0]);
            $diff   = abs($to - $from);
            $years  = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            
            if($days > 7)
                $deleted[] = $notification->_id;
        }
        if(count($deleted) > 0)
            Notification::whereIn('_id',$deleted)->delete();
        
        $this->info('Done !');
        }catch(Exception $e){
            $this->info($e->getMessage());
            return 0;
        }
    }
}
