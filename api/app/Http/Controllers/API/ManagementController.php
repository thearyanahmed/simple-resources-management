<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
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

        $resource = Resource::createResource($data);

        return response()->json([
            'success'  => true,
            'message'  => 'resource created successfully.',
            'resource' => $resource,
        ], Response::HTTP_CREATED);
    }
}
