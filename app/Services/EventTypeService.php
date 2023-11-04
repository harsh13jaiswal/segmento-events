<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;
use Illuminate\Support\Str;

class EventTypeService
{
    protected $bigQueryLib;
    public function __construct(BigqueryLib $bigQueryLib)
    {
        $this->bigQueryLib = $bigQueryLib;
    }

    public function createEventType($input)
    {
        $identifier = substr(Str::uuid()->toString(), -10);
        $base_id = $input['base_id'];
        $type = $input['type'];
        $event_name = $input['event_name'];
        $created_at =  date('Y-m-d H:i:s');
        $event_properties = $input['event_properties'];
        // $updated_at = $input['updated_at'];
        // $updated_by = $input['updated_by'];

        $query = "INSERT INTO `via-socket-prod.segmento.event_types`
        (identifier, base_id, TYPE, event_name, created_at, event_properties)
        VALUES
        ('$identifier', '$base_id', '$type', '$event_name', TIMESTAMP '$created_at', JSON'$event_properties')";

        try {
            return $this->bigQueryLib->runQueryOnDB($query);
        } catch (Exception $e) {
            return "Error in createEventType(): " . $e->getMessage();
        }
    }

    public function getEventTypes($base_id, $id = null)
    {
        $query = "SELECT * FROM via-socket-prod.segmento.event_types WHERE base_id='$base_id' ";
        if ($id) {
            $query .= " AND identifier='$id'";
        }
        try {
            return $this->bigQueryLib->runQueryOnDB($query);
        } catch (Exception $e) {
            return "Error in getEventTypes(): " . $e->getMessage();
        }
    }

    public function deleteEventType($baseId, $id)
    {
        $query = "DELETE FROM via-socket-prod.segmento.event_types WHERE BASE_ID = '$baseId' AND identifier ='$id'";
        try {
            $this->bigQueryLib->runQueryOnDB($query);
            return "Deleted the event type";
        } catch (Exception $e) {
            return "Error in deleteEventType(): " . $e->getMessage();
        }
    }

    public function searchEventType($base_id, $type, $event_name)
    {
        $query = "SELECT * FROM via-socket-prod.segmento.event_type WHERE BASE_ID='$base_id' AND TYPE='$type' AND EVENT_NAME='$event_name";
        try {
            return $this->bigQueryLib->runQueryOnDB($query);
        } catch (Exception $e) {
            return "Error in searchEventType(): " . $e->getMessage();
        }
    }

    public function checkEventTypeExistence($base_id, $type, $event_identifier, $event_name)
    {
        $query = "SELECT EXISTS (SELECT 1 FROM via-socket-prod.`segmento.event_types` WHERE BASE_ID='$base_id' AND EVENT_NAME='$event_name') AS event_exists;";
        $response = $this->bigQueryLib->runQueryOnDB($query);
        $eventExists = $response[0]['event_exists'];
        return $eventExists;
    }
}
