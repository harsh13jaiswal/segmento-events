<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;
use App\Services\EventTypeService;

class EventService{
    protected $bigQueryLib;
    protected $eventTypeService;
    public function __construct(BigqueryLib $bigQueryLib,EventTypeService $eventTypeService) {
        $this->bigQueryLib = $bigQueryLib;
        $this->eventTypeService = $eventTypeService;
    }

    public function getEvents($base_id,$id=null){
        $query="SELECT * FROM via-socket-prod.segmento.event_types WHERE BASE_ID='$base_id' ";
        if($id){
            $query.=" AND identifier='$id'";
        }
        return $this->bigQueryLib->runQueryOnDB($query);
    }

    public function deleteEvents($baseId,$id){
        $query="DELETE FROM via-socket-prod.segmento.event_types
        WHERE BASE_ID = '$baseId' AND identifier ='$id'";
        try{
            $this->bigQueryLib->runQueryOnDB($query);
        }catch(Exception $e){
            return ['something went wrong'];
        }
        return "Event Deleted";
    }

    public function createEvent($input) {
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


        $result=$this->eventTypeService->checkEventTypeExistence($base_id,$type,$identifier,$event_name);
        if(!$result){
            $this->eventTypeService->createEventType($input);
        }

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
        $this->bigQueryLib->runQueryOnDB($query);
    }

    public function filterEvents($query){
        return $this->bigQueryLib->runQueryOnDB($query);
    }

}
