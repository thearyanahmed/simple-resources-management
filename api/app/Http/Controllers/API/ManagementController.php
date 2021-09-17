<?php

namespace App\Http\Controllers\API;

use App\Factories\CreateResourceFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Http\Resources\DeleteResourceResponse;
use App\Http\Resources\SingleResourceCreatedResponse;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ManagementController extends Controller
{
    /**
     * @throws Throwable
     */
    public function store(CreateResourceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $resource = (new CreateResourceFactory($data))->create();

        $res = new SingleResourceCreatedResponse($resource);

        return response()->json($res, Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => 'required|numeric'
        ]);

        abort_if($validator->fails(),404);

        $resource = Resource::with('resourceable')->find($id);

        abort_if(empty($resource), 404);

        DB::beginTransaction();

        try {

            $resource->resourceable()->delete();
            $resource->delete();

            DB::commit();

            $res = new DeleteResourceResponse(null);

            return response()->json($res,Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
