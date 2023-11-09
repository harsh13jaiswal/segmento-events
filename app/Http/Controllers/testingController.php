<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Google\Cloud\BigQuery\BigQueryClient;

use Illuminate\Http\Request;

class testingController extends Controller {
    public function index() {
        $table = "via-socket-prod.segmento.user_events";
        // Temporary data
        $data = [
            [
                'identifier'=>'859',
                'base_id' => '123',
                'user_id'=>'852',
                'event_name' => 'event1',
                'type' => 'type1',
                'created_at' => '2023-11-09 12:00:00',
                'context' => ['data1' => 'value1', 'data2' => 'value2'],
                'page' => ['data3' => 'value3', 'data4' => 'value4'],
                'event_timestamp' => '2023-11-09 12:00:00',
                'event_properties' => ['data5' => 'value5', 'data6' => 'value6']
            ],
            [   
                'identifier'=>'8569',
                'base_id' => '456',
                'user_id'=>'852',
                'event_name' => 'event2',
                'type' => 'type2',
                'created_at' => '2023-11-10 12:00:00',
                'context' => ['data1' => 'value1', 'data2' => 'value2'],
                'page' => ['data3' => 'value3', 'data4' => 'value4'],
                'event_timestamp' => '2023-11-10 12:00:00',
                'event_properties' => ['data5' => 'value5', 'data6' => 'value6']
            ]
        ];

        $insertQuery = $this->generateBulkInsertQuery($table, $data);

        dd($insertQuery);
    }

    public function generateBulkInsertQuery($table, $data) {
        $query = "INSERT INTO $table 
        ( `identifier`,`user_id`,`base_id`, `event_name`, `type`, `created_at`, `context`, `page`, `event_timestamp`, `event_properties`) 
        VALUES ";

        $valueStrings = [];
        foreach ($data as $row) {
            $identifier=$row['identifier'];
            $base_id = $row['base_id'];
            $user_id = $row['user_id'];
            $event_name = $row['event_name'];
            $type = $row['type'];
            $created_at = $row['created_at'];
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
