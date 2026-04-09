<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->name('verification.send');
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tutores
    Route::resource('tutors', 'App\Http\Controllers\TutorController')->names([
        'index' => 'tutors.index',
        'create' => 'tutors.create',
        'store' => 'tutors.store',
        'show' => 'tutors.show',
        'edit' => 'tutors.edit',
        'update' => 'tutors.update',
        'destroy' => 'tutors.destroy',
    ]);

    // Pets
    Route::resource('pets', 'App\Http\Controllers\PetController')->names([
        'index' => 'pets.index',
        'create' => 'pets.create',
        'store' => 'pets.store',
        'show' => 'pets.show',
        'edit' => 'pets.edit',
        'update' => 'pets.update',
        'destroy' => 'pets.destroy',
    ]);

    // Appointments
    Route::resource('appointments', 'App\Http\Controllers\AppointmentController')->names([
        'index' => 'appointments.index',
        'create' => 'appointments.create',
        'store' => 'appointments.store',
        'show' => 'appointments.show',
        'edit' => 'appointments.edit',
        'update' => 'appointments.update',
        'destroy' => 'appointments.destroy',
    ]);

    // Medical Records
    Route::resource('medical-records', 'App\Http\Controllers\MedicalRecordController')->names([
        'index' => 'medical-records.index',
        'create' => 'medical-records.create',
        'store' => 'medical-records.store',
        'show' => 'medical-records.show',
        'edit' => 'medical-records.edit',
        'update' => 'medical-records.update',
        'destroy' => 'medical-records.destroy',
    ]);

    // Vaccinations
    Route::resource('vaccinations', 'App\Http\Controllers\VaccinationController')->names([
        'index' => 'vaccinations.index',
        'create' => 'vaccinations.create',
        'store' => 'vaccinations.store',
        'show' => 'vaccinations.show',
        'edit' => 'vaccinations.edit',
        'update' => 'vaccinations.update',
        'destroy' => 'vaccinations.destroy',
    ]);

    // Exams
    Route::resource('exams', 'App\Http\Controllers\ExamController')->names([
        'index' => 'exams.index',
        'create' => 'exams.create',
        'store' => 'exams.store',
        'show' => 'exams.show',
        'edit' => 'exams.edit',
        'update' => 'exams.update',
        'destroy' => 'exams.destroy',
    ]);

    // Surgeries
    Route::resource('surgeries', 'App\Http\Controllers\SurgeryController')->names([
        'index' => 'surgeries.index',
        'create' => 'surgeries.create',
        'store' => 'surgeries.store',
        'show' => 'surgeries.show',
        'edit' => 'surgeries.edit',
        'update' => 'surgeries.update',
        'destroy' => 'surgeries.destroy',
    ]);

    // Prescriptions
    Route::resource('prescriptions', 'App\Http\Controllers\PrescriptionController')->names([
        'index' => 'prescriptions.index',
        'create' => 'prescriptions.create',
        'store' => 'prescriptions.store',
        'show' => 'prescriptions.show',
        'edit' => 'prescriptions.edit',
        'update' => 'prescriptions.update',
        'destroy' => 'prescriptions.destroy',
    ]);

    // Convênios
    Route::resource('convenios', 'App\Http\Controllers\ConvenioController')->names([
        'index' => 'convenios.index',
        'create' => 'convenios.create',
        'store' => 'convenios.store',
        'show' => 'convenios.show',
        'edit' => 'convenios.edit',
        'update' => 'convenios.update',
        'destroy' => 'convenios.destroy',
    ]);

    // Invoices
    Route::resource('invoices', 'App\Http\Controllers\InvoiceController')->names([
        'index' => 'invoices.index',
        'create' => 'invoices.create',
        'store' => 'invoices.store',
        'show' => 'invoices.show',
        'edit' => 'invoices.edit',
        'update' => 'invoices.update',
        'destroy' => 'invoices.destroy',
    ]);
    
    Route::get('invoices/{invoice}/pix', 'App\Http\Controllers\InvoiceController@generatePix')->name('invoices.pix');

    // Products
    Route::resource('products', 'App\Http\Controllers\ProductController')->names([
        'index' => 'products.index',
        'create' => 'products.create',
        'store' => 'products.store',
        'show' => 'products.show',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy',
    ]);

    // Services
    Route::resource('services', 'App\Http\Controllers\ServiceController')->names([
        'index' => 'services.index',
        'create' => 'services.create',
        'store' => 'services.store',
        'show' => 'services.show',
        'edit' => 'services.edit',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ]);

    // Stock
    Route::get('stock/movements', 'App\Http\Controllers\StockController@movements')->name('stock.movements');

    // Suppliers
    Route::resource('suppliers', 'App\Http\Controllers\SupplierController')->names([
        'index' => 'suppliers.index',
        'create' => 'suppliers.create',
        'store' => 'suppliers.store',
        'show' => 'suppliers.show',
        'edit' => 'suppliers.edit',
        'update' => 'suppliers.update',
        'destroy' => 'suppliers.destroy',
    ]);

    // Categories
    Route::resource('categories', 'App\Http\Controllers\CategoryController')->names([
        'index' => 'categories.index',
        'create' => 'categories.create',
        'store' => 'categories.store',
        'show' => 'categories.show',
        'edit' => 'categories.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    // Reports
    Route::get('reports/financial', 'App\Http\Controllers\ReportController@financial')->name('reports.financial');
    Route::get('reports/export', 'App\Http\Controllers\ReportController@exportPdf')->name('reports.export');

    // Users (Admin)
    Route::resource('users', 'App\Http\Controllers\UserController')->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    // Roles (Admin)
    Route::resource('roles', 'App\Http\Controllers\RoleController')->names([
        'index' => 'roles.index',
        'create' => 'roles.create',
        'store' => 'roles.store',
        'show' => 'roles.show',
        'edit' => 'roles.edit',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy',
    ]);
});
