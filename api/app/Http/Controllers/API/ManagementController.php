<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Models\Resource;

class ManagementController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function store(CreateResourceRequest $request)
    {
        $data = $request->validated();

        // TODO use laravel api resources
        Resource::createResource($data);

        return response()->json([
            'success' => true,
            'message' => 'resource created successfully.'
        ],201);
    }
}
