<?php

use App\Http\Controllers\API\HeartBeat;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', HeartBeat::class);

// public
// admin
