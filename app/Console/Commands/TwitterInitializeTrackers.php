<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Traits\TwitterApiTrait;
use Log;


class TwitterInitializeTrackers extends Command
{
    use TwitterApiTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:initializeTrackers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Twitter Tracker With data from past week';

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
            $trackers = DB::table('trackers')
                ->select('trackers.id')
                ->where('twitter_initialized',"=", false)
                ->whereNotNull('search_terms')
                ->whereRaw('search_terms<>""')
                ->get();
            if(!empty($trackers)){

                foreach($trackers as $tracker){
                    $tweets = $this->updateTwitterTracker($tracker->id);
                    
                }
            }
        }catch(Throwable $t){
            Log::error($t);
        }
        
    }
}
