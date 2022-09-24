<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Routes
Route::get('/user', [\App\Http\Controllers\UserController::class, 'index']);
Route::post('/user', [\App\Http\Controllers\UserController::class, 'store']);
