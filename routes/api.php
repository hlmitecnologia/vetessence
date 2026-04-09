<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TutorController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes - VetEssence Mobile App
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Tutor
    Route::get('/tutors', [TutorController::class, 'index']);
    Route::get('/tutors/{id}', [TutorController::class, 'show']);
    Route::post('/tutors', [TutorController::class, 'store']);

    // Pets
    Route::get('/pets', [PetController::class, 'index']);
    Route::get('/pets/{id}', [PetController::class, 'show']);
    Route::post('/pets', [PetController::class, 'store']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);
});
