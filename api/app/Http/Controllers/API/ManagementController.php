<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'            => 'required|string|max:150',
            'link'             => 'required|url|max:250',
            'opens_in_new_tab' => 'required|boolean',
        ]);



        return response()->json($request->all(),200);
    }
}
