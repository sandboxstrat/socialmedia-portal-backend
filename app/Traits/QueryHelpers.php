<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Log;

trait QueryHelpers{

    //set startDate and endDate if not null, otherwise return query as is
    private function querySetStartEndDates($query, $tableName=null, $startDate=null, $endDate=null ){

        if($tableName!==null){
            $column = $tableName.'.created_at';
        }
        
        if($startDate!=null){

            $startDate = date('Y-m-d',strtotime(urldecode($startDate)));
            
            if($endDate!=null){

                $endDate = date('Y-m-d',strtotime(urldecode($endDate)));

                
                //swap dates if end date is before start date
                if( $endDate < $startDate ){
                    
                    $temp = $endDate;
                    $endDate = $startDate;
                    $startDate = $endDate; 
                
                }

                return($query->whereBetween($column,[$startDate.'00:00:00', $endDate.'23:59:59']));

            }else{

                return($query->where($column,">=",$startDate.'00:00:00'));

            }

        }else{

            return($query);

        }
    }

    private function queryAddTrackerId( $query, $trackerId = null ){

        Log::info($trackerId);

        if( $trackerId !== null && $trackerId != "null" ){

            return( $query->where('tracker_id',"=", $trackerId) );

        }else{

            return $query;
        }

    }

}