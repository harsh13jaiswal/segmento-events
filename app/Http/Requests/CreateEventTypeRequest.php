<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventTypeRequest extends FormRequest
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
            'event_type' =>'required|string',
            'type'=>'required|string|in:track,identify,page',
            'event_properties' =>'required|array'
        ];
    }

    public function validated($key = null, $default = null)
    {

        $input=parent::validated();
        $input['BaseId']=1332;
        $input['identifier']=substr(\Str::uuid()->toString(), -10);
        $input['created_at']=strtotime('now');
        $input['event_properties']=json_encode($input['event_properties']);
        $input['company_id']="1";
        return $input;
    }


}
