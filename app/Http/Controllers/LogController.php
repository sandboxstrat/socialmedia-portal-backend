<?php

namespace App\Http\Controllers;

use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class LogController extends Controller
{
    
    public function getLogs()
    {
        return response()->json(Logs::all());
    }

    public function getOneTracker($id)
    {
        return response()->json(Tracker::find($id));
    }

    public function create(Request $request)
    {        
        $tracker = Tracker::create($request->all());

        return response()->json($tracker, 201);
    }

    public function update($id, Request $request)
    {
        $tracker = Tracker::findOrFail($id);
        $tracker->update($request->all());

        return response()->json($tracker, 200);
    }

    public function delete($id)
    {
        Tracker::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }
}