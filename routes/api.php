<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TwilioRequestValidation;
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

// Routes Used by Twilio
Route::middleware([TwilioRequestValidation::class])->group(function () {
    // User Routes
    Route::get('/user', [\App\Http\Controllers\UserController::class, 'index']);
    Route::post('/user', [\App\Http\Controllers\UserController::class, 'store']);

    // Score Routes
    Route::post('/score', [\App\Http\Controllers\ScoreController::class, 'store']);

    // Scoreboard Route
    Route::post('/scoreboard', \App\Http\Controllers\ScoreBoardController::class);
});

// Frontend Routes
Route::get('/score', [\App\Http\Controllers\ScoreController::class, 'index']);
Route::get('/score/today', \App\Http\Controllers\TodayScoreController::class);
