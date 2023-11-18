<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\FilterEventRequest;
use Illuminate\Http\Request;
use App\Services\EventService;
use App\Http\Resources\CustomResource;
use App\Libs\RabbitMQLib;
use Exception;
use Ixudra\Curl\Facades\Curl;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->data;
        $lib = new RabbitMQLib(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
        foreach ($data as $userEvent) {
            $lib->enqueue('event_logs_queue', $userEvent);
        }
        return 'Event Created';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function filter(FilterEventRequest $request, EventService $es)
    {
        $input = $request->validated();
        $authorization = env('OPEN_AI_TOKEN');
        $host = env('OPEN_AI_HOST');
        $englishQuery = $input['query'];
        
        $operation = '/chat/completions?=null';
        $endpoint = $host . $operation;

        $schema = " 'identifier' (STRING), 'base_id' (STRING), 'user_id' (STRING), 'event_name' (STRING), 'type' (STRING), 'context' (JSON), 'page' (JSON), 'event_timestamp' (TIMESTAMP), event_properties (JSON), and 'created_at' (TIMESTAMP).";
        $tablename = 'via-socket-prod.segmento.user_events';
        $base_id="1";

        $sampleEvent = "
            'type' => 'track',
            'event_properties' => ['productID' => '11'],
            'context' => ['a' => 'asldkjf'],
            'page' => ['a' => 'asldkjf'],
            'user_id' => '1212',
            'event_name' => 'product-purchase',
            'event_timestamp' => '2023-11-16 08:30:00',
            'base_id' => '123123'";
      
        
        // Instructions for generating BigQuery-compatible SQL
        $input = [
            "model" => "gpt-4-1106-preview",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Create SQL queries for Google BigQuery. Focus on correct use of date and timestamp functions. Remember that BigQuery does not support 'MONTH' date part with TIMESTAMP type in TIMESTAMP_SUB. Instead, use DATE_SUB with a DATE type, then convert to TIMESTAMP if needed. The table schema includes: " . $schema . "
                          Sample event JSON: " . $sampleEvent . "
                          Use tablename: " . $tablename . "
                          For JSON parsing, use JSON_EXTRACT_SCALAR correctly.
                          Output format: {'query':'your result',countQuery:'result'}
                          Set a minimum record limit of 5 for each query.
                          English query: " . $englishQuery . "
                          countQuery: It will contain count(*) as count for same english query without limit condition.
                          also add where base_id :".$base_id."
                          Key instructions:
                          1. Adhere strictly to BigQuery functions and syntax.
                          2. Use DATE_SUB for date manipulations and convert to TIMESTAMP if necessary.
                          3. Test for compatibility with Google BigQuery"
                ],
            ],
            "response_format" => [
                "type" => "json_object"
            ]
        ];
    
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization,
        ];

        $result = Curl::to($endpoint)
        ->withData($input)
        ->withHeaders($headers)
        ->asJson()
        ->post();
        // Check if the request was successful
        if (property_exists($result,'error')) {
            return "Something went Wrong";
        }
    
        $result=json_decode($result->choices[0]->message->content);
        $records = $es->filterEvents($result->query);
        $resultCount = $es->filterEvents($result->countQuery);


        return new CustomResource((array) [$records,$resultCount]);
    }
}
