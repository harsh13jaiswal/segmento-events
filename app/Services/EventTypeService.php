<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;

class EventTypeService{
    protected $lib;
    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
    }

    public function searchEventType($base_id,$type,$event_identifier,$event_name){
        // dd($type,$event_identifier,$event_name);
        // error_log('Some message here.');
        $query = "SELECT * FROM via-socket-prod.segmento.event_type WHERE TYPE='$type' AND EVENT_NAME='$event_name";
        return "New event Type Created";
    }
}