<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiLoginController;
use App\Http\Controllers\ApiRegisterController;
use App\Http\Controllers\AttachFreelancerSkillsController;
use App\Http\Controllers\ProfileController;

Route::post('/register/freelancers', [ApiRegisterController::class, 'freelancer']);
Route::post('/register/hire-managers', [ApiRegisterController::class, 'hireManager']);
Route::post('/register/freelancers/{user}', [ApiRegisterController::class, 'freelancerUpdate']);
Route::post('/register/hire-managers/{user}', [ApiRegisterController::class, 'hireManagerUpdate']);

Route::post('/login', [ApiLoginController::class, 'login']);

    Route::get('/profile/freelancer', [ProfileController::class, 'freelancer']);
    Route::get('/profile/hire-manager', [ProfileController::class, 'hireManager']);
    Route::post('/freelancer/skills', AttachFreelancerSkillsController::class);
});
