<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\TwitterTweets;
use App\Traits\QueryHelpers;
use Illuminate\Http\Request;
use Log;

class TwitterController extends Controller
{

    use QueryHelpers;

    public function getTweets(Request $request){
        try{

            $tweets = DB::table('twitter_tweets')
                ->join('twitter_users','twitter_tweets.twitter_user_id','=','twitter_users.id')
                ->join('trackers','twitter_tweets.tracker_id','=','trackers.id')
                ->select(
                    'twitter_users.username as username',
                    'twitter_tweets.id',
                    DB::raw('CONCAT("https://twitter.com/",twitter_users.username,"/status/",twitter_tweets.id) as link'),
                    'twitter_tweets.text',
                    'twitter_tweets.retweet_count',
                    'twitter_tweets.reply_count',
                    'twitter_tweets.like_count',
                    'twitter_tweets.quote_count',
                    'twitter_tweets.impression_count',
                    'twitter_tweets.created_at',
                    'trackers.name as tracker_name',
                );

            $tweets = $this->querySetStartEndDates($tweets,'twitter_tweets',$request['start_date'],$request['end_date']);

            $tweets = $this->queryAddTrackerId($tweets,$request['tracker_id']);

            $tweets = $tweets->get();
            


        }catch(Throwable $t){

            Log::error($t);
        
        }
        
        return response()->json($tweets,200);

    }

    public function getTweetsByUser($userId){

        $userss = DB::table('twitter_tweets')->where('tracker_id', $trackerId);
        return response()->json($tweets);

    }

    public function getTweetCountByDay( Request $request ){

        $tweetCounts = DB::table('twitter_tweets')
            ->selectRaw('DATE_FORMAT(DATE(created_at), "%m/%d/%Y") as date,COUNT(id) as count');
        
        $tweetCounts = $this->querySetStartEndDates($tweetCounts,'twitter_tweets',$request['start_date'],$request['end_date']);

        $tweetCounts = $this->queryAddTrackerId($tweetCounts,$request['tracker_id']);

        $tweetCounts->groupBy('date');

        $tweetCounts = $tweetCounts->get();

        return response()->json($tweetCounts,200);
    }

}