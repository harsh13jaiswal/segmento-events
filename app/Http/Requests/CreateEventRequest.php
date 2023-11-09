<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;
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
            'data' => 'required|array',
            'data.*.type' => 'required|string|in:track,identify,page',
            'data.*.event_properties' => 'required|array',
            'data.*.context' => 'required|array',
            'data.*.page' => 'required|array',
            'data.*.user_id' => 'nullable|string',
            'data.*.event_name' => 'nullable|string',
            'data.*.event_timestamp' => 'required|string',
            'data.*.base_id' => 'required|string',
            
        ];
       
        // , created_at, context, page, original_timestamp, sent_at, received_at, event_properties FROM `via-socket-prod.segmento.user_events` LIMIT 1000

    }

    public function validated($key = null, $default = null)
    {   
        $input=parent::validated();
        $results = []; // Corrected the variable initialization

        foreach ($input['data'] as $item) {
            $result = []; // Corrected the variable initialization within the loop
        
            $item['identifier'] = substr(Str::uuid()->toString(), -10);
            $item['event_properties'] = json_encode($item['event_properties']);
            $item['context'] = json_encode($item['context']);
            $item['page'] = json_encode($item['page']);
            $item['type'] = $item['type'];
            $item['user_id'] = $item['user_id'];
            $item['created_at']=gmdate('Y-m-d H:i:s');
            $item['event_name'] = $item['event_name'];
            $item['event_timestamp'] = $item['event_timestamp'];
            $results[] = $item;
        }
        return $results;  
    }
}
