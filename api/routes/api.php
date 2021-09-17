<?php

use App\Http\Controllers\API\HeartBeat;
use App\Http\Controllers\API\ManagementController;
use App\Http\Controllers\API\VisitorController;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', HeartBeat::class);

Route::get('/resources/{id}',[VisitorController::class,'show'])->name('resources.show');
Route::post('/resources/{id}',[VisitorController::class,'download'])->name('resources.download');

Route::middleware('admin')->group(function(){
    // todo handle, change test endpoints
    Route::get('/resources',function (){
        return response()->json(['status' => 'reached']);
    });

    Route::post('resources',[ManagementController::class,'store'])->name('resources.store');
    Route::delete('resources/{id}',[ManagementController::class,'destroy'])->name('resources.delete');
});



