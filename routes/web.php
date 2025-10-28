<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebugController;

Route::get('/debug-data', [DebugController::class, 'dumpData']);