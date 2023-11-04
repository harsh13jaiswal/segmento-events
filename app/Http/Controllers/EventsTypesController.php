<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateEventTypeRequest;
use App\Http\Requests\UpdateEventTypeRequest;
use App\Services\EventTypeService;

use App\Http\Resources\CustomResource;
use Exception;

class EventsTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, EventTypeService $ets)
    {
        $base_id=$request->base_id;
        $result=$ets->getEventTypes($base_id,null);
        return new CustomResource((array) $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateEventTypeRequest $request,EventTypeService $ets)
    {   
        $input=$request->validated();
        $ets->createEventType($input);
        return "Event Type created"; 
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request,EventTypeService $ets)
    {           
        $base_id=$request->base_id;
        $id=$request->id;
        $result=$ets->getEventTypes($base_id,$id);
        return new CustomResource((array) $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventTypeRequest $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,EventTypeService $ets)
    {   
        $base_id=$request->base_id;
        $id=$request->id;
        $result=$ets->deleteEventType($base_id,$id);
        return new CustomResource((array) [$result]);
    }
}
