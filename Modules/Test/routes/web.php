<?php

use Illuminate\Support\Facades\Route;
use Modules\Test\Http\Controllers\ExampleController;
use Modules\Test\Http\Controllers\TestController;


Route::get('/test', [ExampleController::class, 'index']);
