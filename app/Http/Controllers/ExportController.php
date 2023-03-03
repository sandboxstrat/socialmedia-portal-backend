<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class ExportController extends BaseController
{
    public function createCsv(Request $request, $filename="export.csv"){
        
        $data = json_decode($request->getContent(),true);

        // output headers so that the file is downloaded rather than displayed
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];
        
        //callback function to generate csv
        $callback = function() use ($data){

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            //Adds Byte Order Mark
            fwrite($output, "\xEF\xBB\xBF");

            if(!empty($data['header'])){
                fputcsv($output, $data['header']);
            }

            // loop over the rows, outputting them
            foreach($data['data'] as $row){
                if(is_string($row)){
                    $row=[$row];
                }
                fputcsv($output, $row);
            }

            fclose($output);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
