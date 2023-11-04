<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\BigQuery\BigQueryClient;
use App\Jobs\ExampleJob;

use Illuminate\Http\Request;

class testingController extends Controller
{
    public function index(){
        
        $data="hii";
        ExampleJob::dispatch($data)->onQueue('test')->onConnection('rabbitmq');
        dd("asdf");
    }
}
