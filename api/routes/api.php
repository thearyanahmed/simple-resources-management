<?php

use Illuminate\Support\Facades\Route;

Route::get('/heartbeat',function (){
    return response()->json(['status'=> 'beating'],200);
});
