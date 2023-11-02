<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'auth'],function() {
    Route::post("/registration", RegisterController::class);
    Route::post('/login', LoginController::class);
});

Route::get('/me', MeController::class)->middleware('auth:sanctum');

Route::resource('/rooms', RoomController::class)->middleware('auth:sanctum');

Route::get('/list',[RoomController::class, 'index'])->middleware('auth:sanctum');



