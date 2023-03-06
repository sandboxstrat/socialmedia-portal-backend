<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Traits\TwitterApiTrait;
use Log;


class TwitterUpdateTrackers extends Command
{
    use TwitterApiTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:updateTrackers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Trackers with latest data';

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

            //Updates Trackers where last updated was before yesterday
            $yesterdayDate = date('Y-m-d',strtotime('-1 day'));
            $trackers = DB::table('trackers')
                ->select('trackers.*')
                ->where('twitter_last_updated',"<", $yesterdayDate.' 00:00:00')
                ->where('twitter_initialized',"=", true)
                ->get();
            if(!empty($trackers)){

                //updates tracker with new data
                foreach($trackers as $tracker){
                    $tweets = $this->updateTwitterTracker($tracker->id);
                    
                }
            }
        }catch(Throwable $t){
            Log::error($t);
        }
        
    }
}
