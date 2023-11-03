<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class AddProductCatalogRequest extends FormRequest
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
        // $updated_at = $input['updated_at'];
        return [
            'product_details'=>'required|array',
            'product_id'=>'required|string'
        ];
    }

    public function validated($key = null, $default = null)
    {   
        $input=parent::validated();

        $input['identifier']=substr(Str::uuid()->toString(), -10);
        $input['base_id']=request()->baseId;
        $input['created_at']=date('Y-m-d H:i:s');
        $input['product_details']=json_encode($input['product_details']);
        return $input;  
    }
}
