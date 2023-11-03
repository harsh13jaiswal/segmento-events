<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class filterEventLogRequest extends FormRequest
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
            'query'=>'required|string'
        ];
    }

    public function validated($key = null, $default = null)
    {   
        $input=parent::validated();
       
        return $input;  
    }


}
