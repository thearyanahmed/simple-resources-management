<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\ResourceIsNotDownloadable;
use App\Http\Resources\SingleResourceResponse;
use App\Models\Resource;
use App\Traits\ValidatesIdFromRouteParameter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorController extends Controller
{
    use ValidatesIdFromRouteParameter;

    public function index()
    {
        $resources = Resource::query()
                        ->with('resourceable')
                        ->filter(
                            request()->only('title','resource_type')
                        )
                        ->orderBy($this->orderBy(),$this->orderDirection())
                        ->paginate($this->perPage());

        return SingleResourceResponse::collection($resources);
    }

    public function show($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $res = new SingleResourceResponse($resource);

        return response()->json($res,Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse|StreamedResponse
     */
    public function download($id): JsonResponse|StreamedResponse
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        if(! $resource->isTypeOf(Resource::RESOURCE_FILE)) {

            $res = new ResourceIsNotDownloadable(null);
            return response()->json($res,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $resource->downloadFile();
    }
}
