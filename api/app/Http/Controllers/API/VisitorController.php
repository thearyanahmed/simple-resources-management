<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SingleResourceResponse;
use App\Models\Resource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VisitorController extends Controller
{
    public function show(Resource $resource)
    {
        $resource->load('resourceable');

        $res = new SingleResourceResponse($resource);

        return response()->json($res,Response::HTTP_OK);
    }
}
