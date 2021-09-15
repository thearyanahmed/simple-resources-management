<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResourceRequest;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function store(CreateResourceRequest $request)
    {
        $data = $request->validated();

        dd($data);


        return response()->json($request->all(),200);
    }
}
