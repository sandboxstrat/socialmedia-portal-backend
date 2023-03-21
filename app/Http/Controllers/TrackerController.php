<?php

namespace App\Http\Controllers;

use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class TrackerController extends Controller
{
    
    public function getAllTrackers()
    {
        return response()->json(Tracker::orderBy('name')->get());
    }

    public function getOneTracker($id)
    {
        $trackerData = Tracker::find($id);
        $trackerData->search_terms = $this -> formatSearchTermsForTextbox($trackerData->search_terms);
        return response()->json($trackerData);
    }

    public function create(Request $request)
    {        
        $tracker = Tracker::create($request->all());

        return response()->json($tracker, 201);
    }

    public function update($id, Request $request)
    {
        $requestInfo = $request;
        $requestInfo['search_terms'] = $this->formatSearchTermsForDatabase($request['search_terms']);
        $tracker = Tracker::findOrFail($id);
        $tracker->update($requestInfo->all());

        return response()->json($tracker, 200);
    }

    public function delete($id)
    {
        Tracker::findOrFail($id)->delete();
        DB::table('twitter_tweets')->where('tracker_id','=',$id)->delete();
        return response('Deleted Successfully', 200);
    }

    //Stores search term as json encoded array
    private function formatSearchTermsForDatabase($searchTerms){
        $formattedSearchTerms = explode("\n",$searchTerms);
        return json_encode($formattedSearchTerms);
    }

    //converts json array back to text
    private function formatSearchTermsForTextbox($searchTerms){
        return !empty($searchTerms)?
            implode("\n",json_decode($searchTerms,true)):
            $searchTerms;

    }
}