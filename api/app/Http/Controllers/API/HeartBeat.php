<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeartBeat extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(['status'=> 'beating'],200);
    }
}
