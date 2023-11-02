<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;
use Symfony\Component\CssSelector\Node\FunctionNode;

class ProductCatalogService {

    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
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
        $this->lib->runQuery($query);
    }

    function getProductCatalog($baseId,$productId=null){
        if(empty($productId)) {
            $query = "SELECT * FROM `via-socket-prod.segmento.product_catalogue` WHERE base_id='$baseId' LIMIT 1000;";
        } else {
            $query = "SELECT * FROM `via-socket-prod.segmento.product_catalogue` WHERE product_id='$productId' AND base_id='$baseId';";
        }
        
        return $this->lib->runQuery($query);
    }
}
