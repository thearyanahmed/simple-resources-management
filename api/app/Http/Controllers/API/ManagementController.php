<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateResourceAction;
use App\Actions\DeleteResourceAction;
use App\Actions\UpdateResourceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Http\Resources\DeleteResourceResponse;
use App\Http\Resources\SingleResourceCreatedResponse;
use App\Http\Resources\SingleResourceResponse;
use App\Http\Resources\SingleResourceUpdateResponse;
use App\Models\Resource;
use App\Traits\ValidatesIdFromRouteParameter;
use Illuminate\Http\JsonResponse;
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

    public function edit($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), 404,self::NOT_FOUND_MESSAGE);

        $res = new SingleResourceResponse($resource);

        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateResourceRequest $request,$id): JsonResponse
    {
        $data = $request->validated();

        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $resource = Resource::with('resourceable')->where('id',$id)->filter(['resource_type' => $data['resource_type']])->first();

        abort_if(empty($resource), 404,self::NOT_FOUND_MESSAGE);

        $updatedResource = (new UpdateResourceAction($resource,$resource->resourceable,$data))->edit();

        $res = new SingleResourceUpdateResponse($updatedResource);

        return response()->json($res, Response::HTTP_OK);
    }


    public function destroy($id)
    {
        abort_if(! $this->routeParamIsId($id),Response::HTTP_NOT_FOUND,self::NOT_FOUND_MESSAGE);

        $resource = Resource::find($id);

        abort_if(empty($resource), 404,self::NOT_FOUND_MESSAGE);

        (new DeleteResourceAction($resource))->delete();

        $res = new DeleteResourceResponse(null);

        return response()->json($res,Response::HTTP_OK);
    }

}
