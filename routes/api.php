<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TutorController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\VaccinationController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\ZoonoticDiseaseController;

/*
|--------------------------------------------------------------------------
| API Routes - VetEssence Mobile App
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/online-bookings', [\App\Http\Controllers\Api\OnlineBookingController::class, 'store']);
    Route::get('/online-bookings/availability', [\App\Http\Controllers\Api\OnlineBookingController::class, 'availability']);
    // Lab Equipment (no auth - equipment posts results via webhook)
    Route::post('/lab-equipment/{integrationId}/receive', [\App\Http\Controllers\Api\LabEquipmentController::class, 'receive']);
    Route::get('/lab-equipment/{integrationId}/status', [\App\Http\Controllers\Api\LabEquipmentController::class, 'status']);
    Route::get('/config', function () {
        return response()->json([
            'app_name' => config('app.name'),
            'version' => '1.0.0',
        ]);
    });

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);

        // Tutor
        Route::get('/tutors', [TutorController::class, 'index']);
        Route::get('/tutors/{id}', [TutorController::class, 'show']);
        Route::post('/tutors', [TutorController::class, 'store']);
        Route::put('/tutors/{id}', [TutorController::class, 'update']);
        Route::get('/me/tutor', [TutorController::class, 'myTutor']);

        // Pets
        Route::get('/pets', [PetController::class, 'index']);
        Route::get('/pets/{id}', [PetController::class, 'show']);
        Route::post('/pets', [PetController::class, 'store']);
        Route::put('/pets/{id}', [PetController::class, 'update']);
        Route::get('/me/pets', [PetController::class, 'myPets']);

        // Appointments
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
        Route::get('/appointments/calendar/data', [AppointmentController::class, 'calendar']);
        Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);

        // Vaccinations
        Route::get('/vaccinations', [VaccinationController::class, 'index']);
        Route::get('/vaccinations/{id}', [VaccinationController::class, 'show']);
        Route::post('/vaccinations', [VaccinationController::class, 'store']);
        Route::get('/pets/{petId}/vaccinations', [VaccinationController::class, 'byPet']);

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::get('/my-invoices', [InvoiceController::class, 'myInvoices']);

        // Medical Records
        Route::get('/medical-records', [MedicalRecordController::class, 'index']);
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show']);
        Route::get('/pets/{petId}/medical-records', [MedicalRecordController::class, 'byPet']);

        // Zoonotic Diseases
        Route::get('/zoonotic-diseases', [ZoonoticDiseaseController::class, 'index']);
        Route::get('/zoonotic-diseases/{id}', [ZoonoticDiseaseController::class, 'show']);
        Route::get('/zoonotic-diseases/notifiable/list', [ZoonoticDiseaseController::class, 'notifiable']);
        Route::get('/zoonotic-diseases/categories/list', [ZoonoticDiseaseController::class, 'categories']);

        // Module API
        Route::get('/my-pets/{petId}/weight-records', [\App\Http\Controllers\Api\ModuleApiController::class, 'weightRecords']);
        Route::get('/my-pets/{petId}/hospitalizations', [\App\Http\Controllers\Api\ModuleApiController::class, 'hospitalizations']);
        Route::get('/my-pets/{petId}/treatment-plans', [\App\Http\Controllers\Api\ModuleApiController::class, 'treatmentPlans']);
        Route::get('/my-pets/{petId}/laboratory-orders', [\App\Http\Controllers\Api\ModuleApiController::class, 'laboratoryOrders']);
        Route::get('/my-pets/{petId}/imaging-exams', [\App\Http\Controllers\Api\ModuleApiController::class, 'imagingExams']);
        Route::get('/my-pets/{petId}/referrals', [\App\Http\Controllers\Api\ModuleApiController::class, 'referrals']);
        Route::get('/my-pets/{petId}/consent-forms', [\App\Http\Controllers\Api\ModuleApiController::class, 'consentForms']);


    });
});

// NFSe Webhooks (no auth - called by Webmania)
Route::post('/webhooks/nfse/{branch}', \App\Http\Controllers\Api\NfseWebhookController::class);

// Payment Gateway Webhook (no auth - called by gateway provider)
Route::post('/payments/webhook/{gateway}', [\App\Http\Controllers\Api\PaymentWebhookController::class, 'handle'])
    ->name('api.payments.webhook');
