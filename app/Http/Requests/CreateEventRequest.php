<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type'=>'required|string|in:track,identify,page',
            'event_properties' =>'required|array',
            'context' =>'required|array',
            'page'=>'required|array',
            'user_id'=>'nullable|string',
            'event_name'=>"nullable|string",
            'event_timestamp'=>"required|string",
        ];
       
        // , created_at, context, page, original_timestamp, sent_at, received_at, event_properties FROM `via-socket-prod.segmento.user_events` LIMIT 1000

    }

    public function validated($key = null, $default = null)
    {   
        $input=parent::validated();

        $input['event_identifier']=request()->eventTypeId;
        if(empty($input['user_id'])){
            $input['anonymous_id']=substr(\Str::uuid()->toString(), -10);
        };
        $input['base_id']=1332;
        $input['identifier']=substr(\Str::uuid()->toString(), -10);
        $input['created_at']=date('Y-m-d H:i:s');
        $input['event_properties']=json_encode($input['event_properties']);
        $input['context']=json_encode($input['context']);
        $input['page']=json_encode($input['page']);
        return $input;  
    }
}
