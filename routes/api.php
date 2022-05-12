<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiLoginController;
use App\Http\Controllers\ApiRegisterController;

Route::post('/register/freelancers', [ApiRegisterController::class, 'freelancer']);
Route::post('/register/hire-managers', [ApiRegisterController::class, 'hireManager']);
Route::post('/register/freelancers/{user}', [ApiRegisterController::class, 'freelancerUpdate']);
Route::post('/register/hire-managers/{user}', [ApiRegisterController::class, 'hireManagerUpdate']);

Route::post('/login', [ApiLoginController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
