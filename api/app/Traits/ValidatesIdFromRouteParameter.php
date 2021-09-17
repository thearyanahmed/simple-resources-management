<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesIdFromRouteParameter
{
    public function routeParamIsId($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => 'required|numeric'
        ]);

        return $validator->fails() === false;
    }
}
