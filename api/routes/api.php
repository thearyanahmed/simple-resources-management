<?php

use App\Http\Controllers\API\HeartBeat;
use App\Http\Controllers\API\ManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', HeartBeat::class);

// public
Route::middleware('admin')->group(function(){
    // todo handle, change test endpoints
    Route::get('/resources',function (){
        return response()->json(['status' => 'reached']);
    });

    Route::post('resources',[ManagementController::class,'store'])->name('resources.store');
    Route::delete('resources/{resource}',[ManagementController::class,'destroy'])->name('resources.delete');


});
// admin
