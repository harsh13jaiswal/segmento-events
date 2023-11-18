<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use App\Libs\RabbitMQLib;
use Exception;
use App\Services\EventTypeService;
use Illuminate\Support\Facades\Validator;

class EventService{
    protected $rabbitMQLib;
    protected $bigQueryLib;
    protected $eventTypeService;
    public function __construct() {
        $this->bigQueryLib = new BigqueryLib();
        $this->eventTypeService = new EventTypeService();
    }

    public function initRabbitMQ(){
        $this->rabbitMQLib = new RabbitMQLib(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
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

    public function dequeue($queue){
        $this->rabbitMQLib->dequeue($queue, env('RABBITMQ_BATCH_COUNT'));
        return true;
    }

    public function executeEventLogJob($data){
        $data=$this->bulkValidateEvents($data);
        $query = $this->generateBulkEventInsertQuery($data);
        $this->bigQueryLib->runQueryOnDB($query);
    }
    
    public function generateBulkEventInsertQuery($data) {
        $table="via-socket-prod.segmento.user_events";
        $query = "INSERT INTO $table 
        ( `identifier`,`user_id`,`base_id`, `event_name`, `type`, `created_at`, `context`, `page`, `event_timestamp`, `event_properties`) 
        VALUES ";

        $valueStrings = [];
        foreach ($data as $row) {
            $identifier=substr(\Str::uuid()->toString(), -10);
            $base_id = $row['base_id'];
            $user_id = $row['user_id'];
            $event_name = $row['event_name'];
            $type = $row['type'];
            $event_timestamp = $row['event_timestamp'];
            $created_at = date('Y-m-d H:i:s');
            $context = json_encode($row['context']);
            $page = json_encode($row['page']);
            $event_properties = json_encode($row['event_properties']);

            $valueStrings[] = "('$identifier',  '$user_id', '$base_id', '$event_name', '$type', TIMESTAMP '$created_at', JSON '$context', JSON '$page', TIMESTAMP '$event_timestamp', JSON '$event_properties')";
        }

        $query .= implode(', ', $valueStrings);

        return $query;
    }

    public function bulkValidateEvents($data){  
        $validatedData = collect($data)->reject(function ($item) {
            $validator = Validator::make($item, [
                'type' => 'required|string|in:track,identify,page',
                'event_properties' => 'required|array',
                'context' => 'required|array',
                'page' => 'required|array',
                'user_id' => 'required|string',
                'event_name' => 'required|string',                          //need to apply the event_name validation using cache 
                'event_timestamp' =>'required|date_format:Y-m-d H:i:s',
                'base_id' => 'required|string',                             //need to apply the base_id validation from the db side
            ]);
            return $validator->fails();
        })->toArray();
        return $validatedData;
    }
}
