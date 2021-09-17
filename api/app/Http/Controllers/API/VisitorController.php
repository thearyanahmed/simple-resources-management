<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SingleResourceResponse;
use App\Models\Resource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VisitorController extends Controller
{
    public function show($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => 'required|numeric'
        ]);

        abort_if($validator->fails(),404);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), 404);

        $res = new SingleResourceResponse($resource);

        return response()->json($res,Response::HTTP_OK);
    }
}
