<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateEventTypeRequest;
use App\Http\Requests\UpdateEventTypeRequest;
use App\Services\EventService;
use App\Http\Resources\CustomResource;

class EventsTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, EventService $es )
    {
        $base_id= $request->input('base_id');
        $result=$es->getEvents($base_id,null);
        return new CustomResource((array) $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateEventTypeRequest $request,EventService $es)
    {
        $input=$request->validated();
        $es->createEvent($input);
        return "Event created"; 
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,EventService $es)
    {   
        $result=$es->getEvents(1332,$id);
        return new CustomResource((array) $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventTypeRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,EventService $es)
    {
        $result=$es->deleteEvents(1,$id);
        return new CustomResource((array) [$result]);
    }
}
