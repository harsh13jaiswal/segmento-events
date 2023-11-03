<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;
use App\Services\EventTypeService;

class EventService{
    protected $lib;
    protected $est;
    public function __construct(BigqueryLib $lib,EventTypeService $est) {
        $this->lib = $lib;
        $this->est = $est;
    }

    public function filterEvents($baseId,$query){
        return $this->lib->runQuery($query);
    }

    public function createEventLog($input) {
        $identifier = $input['identifier'];
        $base_id = $input['base_id'];
        $user_id = $input['user_id'];
        $event_name = $input['event_name'];
        $type = $input['type'];
        $created_at = $input['created_at'];
        $context = $input['context'];
        $page = $input['page'];
        $event_timestamp = $input['event_timestamp'];
        $event_name = $input['event_name'];
        $event_properties = $input['event_properties'];
        $table="via-socket-prod.segmento.user_events";


        $this->est->searchEventType($base_id,$type,$identifier,$event_name);


        if(!empty($input['anonymous_id'])){
            $table="via-socket-prod.segmento.anonymous_events";
            $anonymous_id=$input['anonymous_id'];
            $query = "INSERT INTO `$table`
            ( `base_id`, `anonymous_id`, `event_name`, `type`, `created_at`, `context`, `page`, `event_timestamp`, `event_properties`) 
            VALUES 
            ( '$base_id', '$anonymous_id', '$event_name', '$type', TIMESTAMP '$created_at', JSON '$context', JSON '$page', TIMESTAMP '$event_timestamp', JSON '$event_properties')";

        }else{
            $query = "INSERT INTO `$table`
                (`identifier`, `base_id`, `user_id`, `event_name`, `type`, `created_at`, `context`, `page`, `event_timestamp`, `event_properties`) 
                VALUES 
                ('$identifier', '$base_id', '$user_id', '$event_name', '$type', TIMESTAMP '$created_at', JSON '$context', JSON '$page', TIMESTAMP '$event_timestamp', JSON '$event_properties')";
            
        }
        $this->lib->runQuery($query);
    }

    public function runQuery($query){
        $this->lib->runQuery($query);
    }
}
