<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use App\Libs\RabbitMQLib;
use Illuminate\Support\Facades\Storage;

class EventLogsService{
    
    public $lib;
    public $data;
    public function __construct() {
        $this->lib = new RabbitMQLib(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
    }

    public function dequeue($queue){
        $this->lib->dequeue($queue, env('RABBITMQ_BATCH_COUNT'));
        return true;
    }

    public function executeEventLogJob($data){
       $query=generateBulkEventInsertQuery($data);
       $this->bigQueryLib->runQueryOnDB($query);

    }
    
    public function generateBulkEventInsertQuery($data) {
        $table="via-socket-prod.segmento.user_events";
        $query = "INSERT INTO $table 
        ( `identifier`,`user_id`,`base_id`, `event_name`, `type`, `created_at`, `context`, `page`, `event_timestamp`, `event_properties`) 
        VALUES ";

        $valueStrings = [];
        foreach ($data as $row) {
            $identifier=substr(Str::uuid()->toString(), -10);
            $base_id = $row['base_id'];
            $user_id = $row['user_id'];
            $event_name = $row['event_name'];
            $type = $row['type'];
            $created_at = date('Y-m-d H:i:s');
            $context = json_encode($row['context']);
            $page = json_encode($row['page']);
            $event_timestamp = $row['event_timestamp'];
            $event_properties = json_encode($row['event_properties']);

            $valueStrings[] = "('$identifier','$base_id','$user_id', '$event_name', '$type', TIMESTAMP '$created_at', JSON '$context', JSON '$page', TIMESTAMP '$event_timestamp', JSON '$event_properties')";
        }

        $query .= implode(', ', $valueStrings);

        return $query;
    }
}