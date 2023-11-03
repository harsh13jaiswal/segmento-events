<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;

class EventTypeService{
    protected $lib;
    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
    }

    public function createEventType($input){
        
        $identifier = $input['identifier'];
        $base_id = $input['base_id'];
        $type = $input['type'];
        $event_name = $input['event_name'];
        $created_at =  date('Y-m-d H:i:s');
        // $updated_at = $input['updated_at'];
        // $updated_by = $input['updated_by'];
        $event_properties = $input['event_properties'];


        $query = "INSERT INTO `via-socket-prod.segmento.event_types`
        (identifier, company_id, TYPE, event_type, created_at, event_properties)
        VALUES
        ('$identifier', '$base_id', '$type', '$event_name', TIMESTAMP '$created_at', JSON'$event_properties')";
        $this->lib->runQuery($query);
    }

    public function getEventTypes($base_id,$id=null){
        $query="SELECT * FROM via-socket-prod.segmento.event_types WHERE BASE_ID='$base_id' ";
        if($id){
            $query.=" AND identifier='$id'";
        }
        return $this->lib->runQuery($query);
    }

    public function deleteEventType($baseId,$id){
        $query="DELETE FROM via-socket-prod.segmento.event_types
        WHERE BASE_ID = '$baseId' AND identifier ='$id'";
        try{
            $this->lib->runQuery($query);
        }catch(Exception $e){
            return ['something went wrong'];
        }
        return "Event Deleted";
    }

    public function searchEventType($base_id,$type,$event_identifier,$event_name){
        $query = "SELECT * FROM via-socket-prod.segmento.event_type WHERE TYPE='$type' AND EVENT_NAME='$event_name";
        return "New event Type Created";
    }
}