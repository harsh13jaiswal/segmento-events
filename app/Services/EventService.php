<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;

class EventService{
    protected $lib;
    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
    }

    public function createEvent($input){
        $identifier = $input['identifier'];
        $base_id = $input['base_id'];
        $type = $input['type'];
        $event_name = $input['event_name'];
        $created_at =  date('Y-m-d H:i:s');
        // $updated_at = $input['updated_at'];
        // $updated_by = $input['updated_by'];
        $event_properties = $input['event_properties'];
        $query = "INSERT INTO `via-socket-prod`.`segmento`.`event_types`
        (`identifier`, `base_id`, `type`, `event_name`, `created_at`, `event_properties`)
        VALUES
        ('$identifier', '$base_id', '$type', '$event_name', TIMESTAMP '$created_at', JSON'$event_properties')";
        $this->lib->runQuery($query);
    }

    public function getEvents($base_id,$id=null){

        $query="select * from via-socket-prod.segmento.event_types where base_id='$base_id'";
        if($id){
            $query.="and identifier='$id'";
        }
        return $this->lib->runQuery($query);
    }

    public function deleteEvents($baseId,$id){
        $query="DELETE FROM via-socket-prod.segmento.event_types
        WHERE base_id = '$baseId' AND identifier ='$id'";
        try{
            $this->lib->runQuery($query);
        }catch(Exception $e){
            return ['something went wrong'];
        }
        return "Event Deleted";
    }

    public function CreateEventLog($input) {
        $identifier = $input['identifier'];
        $base_id = $input['base_id'];
        $user_id = $input['user_id'];
        $event_name = $input['event_name'];
        $type = $input['type'];
        $created_at = $input['created_at'];
        $context = $input['context'];
        $page = $input['page'];
        $event_timestamp = $input['event_timestamp'];
        $event_properties = $input['event_properties'];
        $table="via-socket-prod.segmento.user_events";

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

    public function filterEvents($baseId,$query){
        return $this->lib->runQuery($query);
    }

    public function runQuery($query){
        $this->lib->runQuery($query);
    }

}
