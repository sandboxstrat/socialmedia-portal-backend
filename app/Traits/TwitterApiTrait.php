<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\TwitterTweets;
use App\Models\TwitterUsers;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Log;

trait TwitterApiTrait{   

    private function searchTwitterApi(Request $request)
    {
        try{
            $searchResponseData=[];
            $tweetDataArray=[];
            $authorDataArray=[];
            $nextToken=null;

            //searches the twitter api, loops as long as there is a next token
            do{
                
                $searchTermsArray = json_decode($request['searchTerm'],true);
                
                $searchTerms = "(".implode(" OR ",$searchTermsArray).")";

                $searchUrl = env('TWITTER_SEARCH_API_ENDPOINT').'query='.urlencode($searchTerms);
                $searchUrl = !empty($request['startDate']&&$request['startDate']!='null')?$searchUrl.'&start_time='.$request['startDate'].'T20:15:00Z':$searchUrl;
                $searchUrl = !empty($request['endDate']&&$request['endDate']!='null')?$searchUrl.'&end_time='.$request['endDate'].'T23:59:59Z':$searchUrl;
                $searchUrl = !empty($nextToken)?$searchUrl.'&next_token='.$nextToken:$searchUrl;
                
                $authorizationHeader = "Authorization: ".env('TWITTER_API_BEARER_TOKEN');
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => $searchUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        $authorizationHeader
                    ],
                ]);
                $response = curl_exec($curl);
                $decodedResponse = json_decode($response,true);

                //Appends new data to responseData array
                foreach($decodedResponse['data'] as $data){
                    $searchResponseData[]=$data['id'];
                }

                //if there is a next token update nextToken to newest token
                $nextToken = !empty($decodedResponse['meta']['next_token'])?$decodedResponse['meta']['next_token']:null;

            }while(!empty($nextToken));

            //break array apart into 100 element chunks
            $searchResponseDataChunks = array_chunk($searchResponseData,100);

            //Checks Twitter api to get full data for each tweet
            foreach($searchResponseDataChunks as $searchResponseDataChunk){
                $tweetIds = implode(",",$searchResponseDataChunk);

                $twitterMultipleTweetsUrl = env('TWITTER_MULTIPLE_TWEETS_API_ENDPOINT').$tweetIds;

                curl_setopt_array($curl, [
                    CURLOPT_URL => $twitterMultipleTweetsUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        $authorizationHeader
                    ],
                ]);

                $response = curl_exec($curl);
                $decodedResponse = json_decode($response,true);

                //reformats data
                foreach($decodedResponse['data'] as $data){

                    $timestampArray = explode("T",$data['created_at']);
                    $date = $timestampArray[0];
                    $timeArray = explode(".",$timestampArray[1]);
                    $time = $timeArray[0];

                    $tweetData= [
                        'id' => $data['id'],
                        'text' => $data['text'],
                        'author_id' => $data['author_id'],
                        'retweet_count' => $data['public_metrics']['retweet_count'],
                        'reply_count' => $data['public_metrics']['reply_count'],
                        'like_count' => $data['public_metrics']['like_count'],
                        'quote_count' => $data['public_metrics']['quote_count'],
                        'impression_count' => $data['public_metrics']['impression_count'],
                        'created_at' => $date.' '.$time
                    ];
                    
                    $tweetDataArray[$data['id']] = $tweetData;
                }

                //reformats user info
                foreach($decodedResponse['includes']['users'] as $user){
                    $userData = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'username' => $user['username']
                    ];
                    
                    $userDataArray[$user['id']] =  $userData;
                }
            }

                $returnData = [
                    'tweets' => $tweetDataArray,
                    'users' => $userDataArray,
                    'count' => count($tweetDataArray) 
                ];
            
                curl_close($curl);
                return($returnData);
            
        }catch(Throwable $t){
            Log::info($t);
        }
        
    }

    private function saveTwitterTweet($tweetInfo,$trackerId){

        $request = new Request;
        $request->replace([
            'id' => $tweetInfo['id'],
            'twitter_user_id' => $tweetInfo['author_id'],
            'text' => $tweetInfo['text'],
            'retweet_count' => $tweetInfo['retweet_count'],
            'reply_count' => $tweetInfo['reply_count'],
            'like_count' => $tweetInfo['like_count'],
            'quote_count' => $tweetInfo['quote_count'],
            'impression_count' => $tweetInfo['impression_count'],
            'created_at' => $tweetInfo['created_at'],
            'tracker_id'=>$trackerId,
        ]);

        $twitterTweets = TwitterTweets::upsert( $request->all(), ['id' =>$tweetInfo['id']] );
        
    }

    private function saveTwitterUser($userInfo){

        $request = new Request;
        $request->replace([
            'id' => $userInfo['id'],
            'name' => $userInfo['name'],
            'username' => $userInfo['username'],
        ]);

        $twitterTweets = TwitterUsers::upsert( $request->all(), ['id' => $userInfo['id'] ]);
    }

    function getTweetsforTracker($trackerId){

    }

    function updateTwitterTracker($trackerId){

        $tracker = Tracker::find($trackerId);

        $requestArray = [
            'searchTerm' => $tracker->search_terms,
        ];

        //adds start dates and end dates if tracker is initialized
        if($tracker->twitter_initialized==true){
            
            $requestArray['startDate'] =  date('Y-m-d',strtotime("$tracker->twitter_last_updated-1 days"));

        }

        $request = new Request;
        $request->replace($requestArray);

        //search twitter api and update tweets and users
        $twitterData = $this->searchTwitterApi($request);

        foreach($twitterData['tweets'] as $tweet){
            $this->saveTwitterTweet($tweet,$trackerId);
        }
        
        foreach($twitterData['users'] as $user){
            $this->saveTwitterUser($user);
        }

        //update tracker with latest date
        $tracker->twitter_last_updated=date('Y-m-d');
        $tracker->twitter_initialized=true;
        $tracker->save();

        return response()->json($twitterData, 200);

    }
}