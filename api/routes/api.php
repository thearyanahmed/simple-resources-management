<?php

use App\Http\Controllers\API\HeartBeat;
use App\Http\Controllers\API\ManagementController;
use App\Http\Controllers\API\VisitorController;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', HeartBeat::class);

Route::get('/resources',[VisitorController::class,'index'])->name('resources.index');

Route::get('/resources/{id}',[VisitorController::class,'show'])->name('resources.show');
Route::post('/resources/{id}/download',[VisitorController::class,'download'])->name('resources.download');

Route::middleware('admin')->group(function(){
    Route::post('resources',[ManagementController::class,'store'])->name('resources.store');
    Route::get('resources/{id}/edit',[ManagementController::class,'edit'])->name('resources.edit');
    Route::put('resources/{id}',[ManagementController::class,'update'])->name('resources.update');
    Route::delete('resources/{id}',[ManagementController::class,'destroy'])->name('resources.delete');
});



