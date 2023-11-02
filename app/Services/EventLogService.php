<?php

namespace App\Services;

use App\Libs\BigqueryLib;
use Exception;


class EventLogService {

    public function __construct(BigqueryLib $lib) {
        $this->lib = $lib;
    }

    
}
