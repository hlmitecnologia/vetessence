<?php

use App\Http\Controllers\Portal\Auth\LoginController;
use App\Http\Controllers\Portal\Auth\RegisterController;
use App\Http\Controllers\Portal\Auth\ForgotPasswordController;
use App\Http\Controllers\Portal\Auth\ResetPasswordController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\PetController;
use App\Http\Controllers\Portal\AppointmentController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\MedicalRecordController;
use App\Http\Controllers\Portal\ExamController;
use App\Http\Controllers\Portal\PrescriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:tutor')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('portal.login');
    Route::post('login', [LoginController::class, 'store'])->name('portal.login.store');

    Route::get('register', [RegisterController::class, 'create'])->name('portal.register');
    Route::post('register', [RegisterController::class, 'store'])->name('portal.register.store');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('portal.password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('portal.password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('portal.password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('portal.password.update');
});

Route::middleware('auth:tutor')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('portal.logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('portal.dashboard');

    Route::get('pets', [PetController::class, 'index'])->name('portal.pets.index');
    Route::get('pets/{id}', [PetController::class, 'show'])->name('portal.pets.show');

    Route::get('appointments', [AppointmentController::class, 'index'])->name('portal.appointments.index');
    Route::get('appointments/create', [AppointmentController::class, 'create'])->name('portal.appointments.create');

    Route::get('invoices', [InvoiceController::class, 'index'])->name('portal.invoices.index');
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('portal.invoices.show');

    Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('portal.medical-records.index');
    Route::get('medical-records/{id}', [MedicalRecordController::class, 'show'])->name('portal.medical-records.show');

    Route::get('exams', [ExamController::class, 'index'])->name('portal.exams.index');

    Route::get('prescriptions', [PrescriptionController::class, 'index'])->name('portal.prescriptions.index');
});
