<?php

use App\Http\Controllers\API\HeartBeat;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', HeartBeat::class);

// public
Route::middleware('admin')->group(function(){
    Route::get('/resources',function (){
        return response()->json(['status' => 'reached']);
    });
});
// admin
