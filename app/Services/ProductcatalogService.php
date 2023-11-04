<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;
use Symfony\Component\CssSelector\Node\FunctionNode;

class ProductCatalogService{
    protected $bigQueryLib;
    public function __construct(BigqueryLib $bigQueryLib) {
        $this->bigQueryLib = $bigQueryLib;
    }

    function addProductToCatalogue($input)
    {
        // Extract the input data
        $identifier = $input['identifier'];
        $base_id = $input['base_id'];
        $product_id = $input['product_id'];
        $created_at = $input['created_at'];
        $product_details = $input['product_details'];

        // Prepare and execute the SQL query
        $query = "INSERT INTO `via-socket-prod.segmento.product_catalogue` 
                (`identifier`, `base_id`, `product_id`, `created_at`, `product_details`) 
                VALUES 
                ('$identifier', '$base_id', '$product_id', TIMESTAMP'$created_at', JSON'$product_details')";
        $this->bigQueryLib->runQueryOnDB($query);
    }

    function getProductCatalog($base_id,$product_id=null){
        if(empty($product_id)) {
            $query = "SELECT * FROM `via-socket-prod.segmento.product_catalogue` WHERE base_id='$base_id' LIMIT 100;";
        } else {
            $query = "SELECT * FROM `via-socket-prod.segmento.product_catalogue` WHERE product_id='$product_id' AND base_id='$base_id';";
        }
        
        return $this->bigQueryLib->runQueryOnDB($query);
    }
}
