<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use App\Models\Resource;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function store(CreateResourceRequest $request)
    {
        $data = $request->validated();

        // TODO use laravel api resources
        $res = Resource::createResource($data);

        return response()->json($res,201);
    }
}
