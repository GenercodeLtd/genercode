<?php

namespace GenerCode;

use \GenerCode\Exceptions as Exceptions;

class Repository
{

   
    protected function audit($id, $action, ?array $data = null)
    {
        $data = ($data) ? json_encode($data) : "{}";
    
        $model = new \GenerCode\Models\Audit();
        $model->withAuthen()->create($data);
    }

}
