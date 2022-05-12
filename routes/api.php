<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiLoginController;
use App\Http\Controllers\ApiRegisterController;
use App\Http\Controllers\AttachFreelancerSkillsController;
use App\Http\Controllers\DraftJobController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublishJobController;
use App\Http\Controllers\ShowSkillsController;

Route::post('/register/freelancers', [ApiRegisterController::class, 'freelancer']);
Route::post('/register/hire-managers', [ApiRegisterController::class, 'hireManager']);
Route::post('/register/freelancers/{user}', [ApiRegisterController::class, 'freelancerUpdate']);
Route::post('/register/hire-managers/{user}', [ApiRegisterController::class, 'hireManagerUpdate']);

Route::post('/login', [ApiLoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile/freelancer', [ProfileController::class, 'freelancer']);
    Route::get('/profile/hire-manager', [ProfileController::class, 'hireManager']);

    Route::get('skills', ShowSkillsController::class);

});

Route::middleware(['auth:sanctum', 'freelancer'])->group(function () {
    Route::post('/freelancer/skills', AttachFreelancerSkillsController::class);
});

Route::middleware(['auth:sanctum', 'hire-manager'])->group(function () {
    Route::post('/jobs/draft', [DraftJobController::class, 'store']);
    Route::post('/jobs/draft/{job}', [DraftJobController::class, 'update']);

    Route::post('/jobs/publish', [PublishJobController::class, 'store']);
    Route::post('/jobs/publish/{job}', [PublishJobController::class, 'update']);
});
