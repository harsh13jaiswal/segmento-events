<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;

class EventService{
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

    public function UpdateEvent($input){

    }

    public function getEvents($base_id,$id=null){

        $query="select * from via-socket-prod.segmento.event_types where base_id='$base_id'";
        if($id){
            $query.="and identifier='$id'";
        }
        return $this->lib->runQuery($query);
    }

    public function deleteEvents($companyId,$id){
        $query="DELETE FROM via-socket-prod.segmento.event_types
        WHERE company_id = '$companyId' AND identifier ='$id'";
        try{
            $this->lib->runQuery($query);
        }catch(Exception $e){
            return ['something went wrong'];
        }
        return "Event Deleted";
    }
    public function runQuery($query){
        $this->lib->runQuery($query);
    }





}
