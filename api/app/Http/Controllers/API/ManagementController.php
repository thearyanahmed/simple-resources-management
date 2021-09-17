<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateResourceAction;
use App\Actions\DeleteResourceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Http\Resources\DeleteResourceResponse;
use App\Http\Resources\SingleResourceCreatedResponse;
use App\Models\Resource;
use App\Traits\ValidatesIdFromRouteParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ManagementController extends Controller
{
    use ValidatesIdFromRouteParameter;
    /**
     * @throws Throwable
     */
    public function store(CreateResourceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $resource = (new CreateResourceAction($data))->create();

        $res = new SingleResourceCreatedResponse($resource);

        return response()->json($res, Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND);

        $resource = Resource::find($id);

        abort_if(empty($resource), 404);

        (new DeleteResourceAction($resource))->delete();

        $res = new DeleteResourceResponse(null);

        return response()->json($res,Response::HTTP_OK);
    }
}
