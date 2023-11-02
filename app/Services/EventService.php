<?php

namespace App\Services;

use App\Libs\BigqueryLib;

class EventService{
    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
    }

    public function createEvent($input){
        $identifier = $input['identifier'];
        $company_id = $input['company_id'];
        $type = $input['type'];
        $event_type = $input['event_type'];
        $created_at =  date('Y-m-d H:i:s');
        // $updated_at = $input['updated_at'];
        // $updated_by = $input['updated_by'];
        $event_properties = $input['event_properties'];

        $query = "INSERT INTO `via-socket-prod.segmento.event_types`
        (identifier, company_id, type, event_type, created_at, event_properties)
        VALUES
        ('$identifier', '$company_id', '$type', '$event_type', TIMESTAMP('$created_at'), JSON'$event_properties')";

        $this->lib->runQuery($query);
    }

    public function UpdateEvent($input){

    }

    public function getEvents($companyId,$id=null){

        $query="select * from via-socket-prod.segmento.event_types where company_id='1'";
        if($id){
            $query.="and identifier='$id'";
        }
        return $this->lib->runQuery($query);
    }

    public function deleteEvents($companyId,$id){
        $query="DELETE FROM via-socket-prod.segmento.event_types
        WHERE company_id = '$companyId' AND identifier ='$id'";

        return $this->lib->runQuery($query);
    }
    public function runQuery($query){
        $this->lib->runQuery($query);
    }





}
