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
        Storage::disk('local')->put('example.txt', json_encode($data));
        // $dbconn = new BigqueryLib();
        // $dbconn->bulkInsert($data);
    }
}