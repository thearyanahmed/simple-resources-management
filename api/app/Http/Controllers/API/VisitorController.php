<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceIsNotDownloadable;
use App\Http\Resources\SingleResourceResponse;
use App\Models\Resource;
use App\Traits\ValidatesIdFromRouteParameter;
use Symfony\Component\HttpFoundation\Response;

class VisitorController extends Controller
{
    use ValidatesIdFromRouteParameter;

    public function show($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), Response::HTTP_NOT_FOUND);

        $res = new SingleResourceResponse($resource);

        return response()->json($res,Response::HTTP_OK);
    }

    public function download($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), Response::HTTP_NOT_FOUND);

        if(! $resource->isTypeOf(Resource::RESOURCE_FILE)) {

            $res = new ResourceIsNotDownloadable(null);
            return response()->json($res,Response::HTTP_UNPROCESSABLE_ENTITY);
        }


    }
}
