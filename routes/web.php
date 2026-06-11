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

// Public prescription verification (QR code scan) — no auth required, rate-limited
Route::get('r/{hash}', 'App\Http\Controllers\PublicPrescriptionController@verify')
    ->name('prescriptions.verify')
    ->middleware('throttle:10,1');

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->name('verification.send');
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Documentation diagrams (BPMN SVGs) — slug sem extensão para nginx não interceptar
Route::get('docs/imagem/{slug}', function (string $slug) {
    $slug = basename($slug);
    $path = storage_path("docs/diagrams/{$slug}.svg");
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'image/svg+xml']);
})->name('docs.diagrams');

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
    Route::get('pets/{pet}/timeline', 'App\Http\Controllers\PatientTimelineController@index')->name('pets.timeline');

    // Patient Flow Board (must be before appointments resource)
    Route::get('appointments/flow-data', 'App\Http\Controllers\AppointmentController@flowData')->name('appointments.flow-data');
    Route::get('appointments/flow-board', 'App\Http\Controllers\AppointmentController@flowBoard')->name('appointments.flow-board');
    Route::patch('appointments/{appointment}/status', 'App\Http\Controllers\AppointmentController@updateStatus')->name('appointments.update-status');

    // Appointments
    Route::get('appointments/calendar-json', 'App\Http\Controllers\Api\AppointmentController@calendar')->name('appointments.calendar-json');
    Route::resource('appointments', 'App\Http\Controllers\AppointmentController')->names([
        'index' => 'appointments.index',
        'create' => 'appointments.create',
        'store' => 'appointments.store',
        'show' => 'appointments.show',
        'edit' => 'appointments.edit',
        'update' => 'appointments.update',
        'destroy' => 'appointments.destroy',
    ]);
    Route::put('appointments/{appointment}/reschedule', 'App\Http\Controllers\AppointmentController@reschedule');

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
    Route::post('medical-records/{medicalRecord}/generate-invoice', 'App\Http\Controllers\MedicalRecordController@generateInvoice')
        ->name('medical-records.generate-invoice');

    // Vaccinations
    Route::get('vaccinations/forecast', 'App\Http\Controllers\VaccinationController@forecast')->name('vaccinations.forecast');
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
    Route::post('invoices/{invoice}/pay', 'App\Http\Controllers\InvoiceController@pay')->name('invoices.pay');
    Route::post('invoices/{invoice}/cancel', 'App\Http\Controllers\InvoiceController@cancel')->name('invoices.cancel');

    // NFSe
    Route::get('nfse', 'App\Http\Controllers\NfseController@index')->name('nfse.index')->middleware('can:nfse.view');
    Route::get('nfse/config', 'App\Http\Controllers\NfseConfigController@edit')->name('nfse.config')->middleware('can:nfse-config.edit');
    Route::put('nfse/config', 'App\Http\Controllers\NfseConfigController@update')->name('nfse.config.update')->middleware('can:nfse-config.edit');
    Route::get('nfse/export', 'App\Http\Controllers\NfseController@exportForm')->name('nfse.export-form')->middleware('can:nfse.view');
    Route::post('nfse/export', 'App\Http\Controllers\NfseController@export')->name('nfse.export')->middleware('can:nfse.view');
    Route::get('nfse/{nfseInvoice}', 'App\Http\Controllers\NfseController@show')->name('nfse.show')->middleware('can:nfse.view');
    Route::get('nfse/{nfseInvoice}/xml', 'App\Http\Controllers\NfseController@downloadXml')->name('nfse.download-xml')->middleware('can:nfse.view');
    Route::get('nfse/{nfseInvoice}/pdf', 'App\Http\Controllers\NfseController@downloadPdf')->name('nfse.download-pdf')->middleware('can:nfse.view');
    Route::post('invoices/{invoice}/nfse-emitir', 'App\Http\Controllers\NfseController@emitir')->name('nfse.emitir')->middleware('can:nfse.emit');
    Route::post('invoices/{invoice}/nfse-cancelar', 'App\Http\Controllers\NfseController@cancelar')->name('nfse.cancelar')->middleware('can:nfse.cancel');

    // NF-e
    Route::get('nfe', 'App\Http\Controllers\NfeController@index')->name('nfe.index')->middleware('can:nfe.view');
    Route::get('nfe/config', 'App\Http\Controllers\NfeConfigController@edit')->name('nfe.config')->middleware('can:nfe-config.edit');
    Route::put('nfe/config', 'App\Http\Controllers\NfeConfigController@update')->name('nfe.config.update')->middleware('can:nfe-config.edit');
    Route::get('nfe/export', 'App\Http\Controllers\NfeController@exportForm')->name('nfe.export-form')->middleware('can:nfe.view');
    Route::post('nfe/export', 'App\Http\Controllers\NfeController@export')->name('nfe.export')->middleware('can:nfe.view');
    Route::get('nfe/{nfeInvoice}', 'App\Http\Controllers\NfeController@show')->name('nfe.show')->middleware('can:nfe.view');
    Route::get('nfe/{nfeInvoice}/xml', 'App\Http\Controllers\NfeController@downloadXml')->name('nfe.download-xml')->middleware('can:nfe.view');
    Route::get('nfe/{nfeInvoice}/pdf', 'App\Http\Controllers\NfeController@downloadPdf')->name('nfe.download-pdf')->middleware('can:nfe.view');
    Route::get('nfe/{nfeInvoice}/danfe', 'App\Http\Controllers\NfeController@downloadDanfe')->name('nfe.download-danfe')->middleware('can:nfe.view');
    Route::post('invoices/{invoice}/nfe-emitir', 'App\Http\Controllers\NfeController@emitir')->name('nfe.emitir')->middleware('can:nfe.emit');
    Route::post('invoices/{invoice}/nfe-cancelar', 'App\Http\Controllers\NfeController@cancelar')->name('nfe.cancelar')->middleware('can:nfe.cancel');

    // Bank Reconciliation
    Route::resource('bank-accounts', 'App\Http\Controllers\BankAccountController')->names([
        'index' => 'bank-accounts.index',
        'create' => 'bank-accounts.create',
        'store' => 'bank-accounts.store',
        'show' => 'bank-accounts.show',
        'edit' => 'bank-accounts.edit',
        'update' => 'bank-accounts.update',
        'destroy' => 'bank-accounts.destroy',
    ]);
    Route::get('bank-reconciliation', 'App\Http\Controllers\BankReconciliationController@index')->name('bank-reconciliation.index');
    Route::post('bank-reconciliation/{bankTransaction}/match', 'App\Http\Controllers\BankReconciliationController@match')->name('bank-reconciliation.match');
    Route::post('bank-reconciliation/{bankTransaction}/unmatch', 'App\Http\Controllers\BankReconciliationController@unmatch')->name('bank-reconciliation.unmatch');
    Route::get('bank-reconciliation/{bankAccount}/suggest', 'App\Http\Controllers\BankReconciliationController@suggest')->name('bank-reconciliation.suggest');

    // Commissions
    Route::get('commissions', 'App\Http\Controllers\CommissionController@index')->name('commissions.index');
    Route::get('commissions/{commissionLog}', 'App\Http\Controllers\CommissionController@show')->name('commissions.show');
    Route::post('commissions/{commissionLog}/mark-paid', 'App\Http\Controllers\CommissionController@markPaid')->name('commissions.mark-paid');
    Route::get('commissions/rates/list', 'App\Http\Controllers\CommissionController@rates')->name('commissions.rates');
    Route::post('commissions/rates', 'App\Http\Controllers\CommissionController@ratesStore')->name('commissions.rates-store');
    Route::delete('commissions/rates/{commissionRate}', 'App\Http\Controllers\CommissionController@ratesDestroy')->name('commissions.rates-destroy');

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
    Route::put('services/type-map/{type}', 'App\Http\Controllers\ServiceController@updateTypeMap')
        ->name('services.type-map.update');

    // Stock
    Route::get('stock/movements', 'App\Http\Controllers\StockController@movements')->name('stock.movements');
    Route::get('stock/transfer', 'App\Http\Controllers\StockController@transferForm')->name('stock.transfer-form');
    Route::post('stock/transfer', 'App\Http\Controllers\StockController@transfer')->name('stock.transfer');

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

    // Geographic data (cities by state for cascading selects)
    Route::get('/api/cities/{stateId}', function ($stateId) {
        return \App\Models\City::where('state_id', $stateId)
            ->orderBy('name')
            ->pluck('name', 'id');
    })->name('api.cities.by-state');

    // CEP lookup (auto-fill address from zipcode)
    Route::get('/api/cep/{cep}', function ($cep) {
        $result = app(\App\Services\Cep\CepService::class)->lookup($cep);
        if (!$result) {
            return response()->json(['error' => 'CEP not found'], 404);
        }
        return response()->json($result->toArray());
    })->name('api.cep.lookup');

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

    // Zoonotic Diseases
    Route::resource('zoonotic-diseases', 'App\Http\Controllers\ZoonoticDiseaseController')->names([
        'index' => 'zoonotic-diseases.index',
        'create' => 'zoonotic-diseases.create',
        'store' => 'zoonotic-diseases.store',
        'show' => 'zoonotic-diseases.show',
        'edit' => 'zoonotic-diseases.edit',
        'update' => 'zoonotic-diseases.update',
        'destroy' => 'zoonotic-diseases.destroy',
    ]);

    // Hospitalization
    Route::resource('hospitalizations', 'App\Http\Controllers\HospitalizationController')->names([
        'index' => 'hospitalizations.index',
        'create' => 'hospitalizations.create',
        'store' => 'hospitalizations.store',
        'show' => 'hospitalizations.show',
        'edit' => 'hospitalizations.edit',
        'update' => 'hospitalizations.update',
        'destroy' => 'hospitalizations.destroy',
    ]);
    Route::get('hospitalization-daily-records', 'App\Http\Controllers\HospitalizationDailyRecordController@index')
        ->name('hospitalization-daily-records.index');
    Route::get('hospitalization-daily-records/create', 'App\Http\Controllers\HospitalizationDailyRecordController@create')
        ->name('hospitalization-daily-records.create');
    Route::post('hospitalizations/{hospitalization}/daily-records', 'App\Http\Controllers\HospitalizationDailyRecordController@store')
        ->name('hospitalizations.daily-records.store');
    Route::post('hospitalization-daily-records', 'App\Http\Controllers\HospitalizationDailyRecordController@store')
        ->name('hospitalization-daily-records.store');
    Route::get('hospitalization-daily-records/{hospitalizationDailyRecord}', 'App\Http\Controllers\HospitalizationDailyRecordController@show')
        ->name('hospitalization-daily-records.show');
    Route::get('hospitalization-daily-records/{hospitalizationDailyRecord}/edit', 'App\Http\Controllers\HospitalizationDailyRecordController@edit')
        ->name('hospitalization-daily-records.edit');
    Route::put('hospitalization-daily-records/{hospitalizationDailyRecord}', 'App\Http\Controllers\HospitalizationDailyRecordController@update')
        ->name('hospitalization-daily-records.update');
    Route::delete('hospitalizations/daily-records/{dailyRecord}', 'App\Http\Controllers\HospitalizationDailyRecordController@destroy')
        ->name('hospitalizations.daily-records.destroy');
    Route::delete('hospitalization-daily-records/{hospitalizationDailyRecord}', 'App\Http\Controllers\HospitalizationDailyRecordController@destroy')
        ->name('hospitalization-daily-records.destroy');
    Route::resource('hospitalizations.fluid-therapies', 'App\Http\Controllers\HospitalizationFluidTherapyController')
        ->only(['store', 'destroy'])->names(['store' => 'hospitalizations.fluid-therapies.store', 'destroy' => 'hospitalizations.fluid-therapies.destroy']);
    Route::resource('hospitalizations.prescriptions', 'App\Http\Controllers\HospitalizationPrescriptionController')
        ->except(['index', 'show'])->names(['store' => 'hospitalizations.prescriptions.store', 'update' => 'hospitalizations.prescriptions.update', 'destroy' => 'hospitalizations.prescriptions.destroy']);

    // Vaccination Reminders
    Route::resource('vaccination-reminders', 'App\Http\Controllers\VaccinationReminderController')->names([
        'index' => 'vaccination-reminders.index',
        'create' => 'vaccination-reminders.create',
        'store' => 'vaccination-reminders.store',
        'show' => 'vaccination-reminders.show',
        'edit' => 'vaccination-reminders.edit',
        'update' => 'vaccination-reminders.update',
        'destroy' => 'vaccination-reminders.destroy',
    ]);

    // Notification Logs
    Route::get('notification-logs', 'App\Http\Controllers\NotificationLogController@index')->name('notification-logs.index');
    Route::get('notification-logs/{notificationLog}', 'App\Http\Controllers\NotificationLogController@show')->name('notification-logs.show');
    Route::delete('notification-logs/{notificationLog}', 'App\Http\Controllers\NotificationLogController@destroy')->name('notification-logs.destroy');

    // Weight Records
    Route::get('weight-records', 'App\Http\Controllers\WeightRecordController@index')->name('weight-records.index');
    Route::get('weight-records/create', 'App\Http\Controllers\WeightRecordController@create')->name('weight-records.create');
    Route::post('weight-records', 'App\Http\Controllers\WeightRecordController@store')->name('weight-records.store');
    Route::get('weight-records/{weightRecord}', 'App\Http\Controllers\WeightRecordController@show')->name('weight-records.show');
    Route::get('weight-records/{weightRecord}/edit', 'App\Http\Controllers\WeightRecordController@edit')->name('weight-records.edit');
    Route::put('weight-records/{weightRecord}', 'App\Http\Controllers\WeightRecordController@update')->name('weight-records.update');
    Route::delete('weight-records/{weightRecord}', 'App\Http\Controllers\WeightRecordController@destroy')->name('weight-records.destroy');
    Route::get('weight-records/{pet}/chart-data', 'App\Http\Controllers\WeightRecordController@chartData')->name('weight-records.chart');

    // Treatment Plans
    Route::resource('treatment-plans', 'App\Http\Controllers\TreatmentPlanController')->names([
        'index' => 'treatment-plans.index',
        'create' => 'treatment-plans.create',
        'store' => 'treatment-plans.store',
        'show' => 'treatment-plans.show',
        'edit' => 'treatment-plans.edit',
        'update' => 'treatment-plans.update',
        'destroy' => 'treatment-plans.destroy',
    ]);
    Route::put('treatment-plans/{treatmentPlan}/approve', 'App\Http\Controllers\TreatmentPlanController@approve')->name('treatment-plans.approve');
    Route::put('treatment-plans/{treatmentPlan}/reject', 'App\Http\Controllers\TreatmentPlanController@reject')->name('treatment-plans.reject');

    // Consent Forms
    Route::resource('consent-forms', 'App\Http\Controllers\ConsentFormController')->names([
        'index' => 'consent-forms.index',
        'create' => 'consent-forms.create',
        'store' => 'consent-forms.store',
        'show' => 'consent-forms.show',
        'edit' => 'consent-forms.edit',
        'update' => 'consent-forms.update',
        'destroy' => 'consent-forms.destroy',
    ]);
    Route::get('consent-forms/{consentForm}/preview', 'App\Http\Controllers\ConsentFormController@preview')->name('consent-forms.preview');

    // Consent Templates
    Route::resource('consent-templates', 'App\Http\Controllers\ConsentTemplateController')->names([
        'index' => 'consent-templates.index',
        'create' => 'consent-templates.create',
        'store' => 'consent-templates.store',
        'show' => 'consent-templates.show',
        'edit' => 'consent-templates.edit',
        'update' => 'consent-templates.update',
        'destroy' => 'consent-templates.destroy',
    ]);

    // Dental Charts
    Route::resource('dental-charts', 'App\Http\Controllers\DentalChartController')->names([
        'index' => 'dental-charts.index',
        'create' => 'dental-charts.create',
        'store' => 'dental-charts.store',
        'show' => 'dental-charts.show',
        'edit' => 'dental-charts.edit',
        'update' => 'dental-charts.update',
        'destroy' => 'dental-charts.destroy',
    ]);

    // Controlled Substances
    Route::resource('controlled-substances', 'App\Http\Controllers\ControlledSubstanceController')->names([
        'index' => 'controlled-substances.index',
        'create' => 'controlled-substances.create',
        'store' => 'controlled-substances.store',
        'show' => 'controlled-substances.show',
        'edit' => 'controlled-substances.edit',
        'update' => 'controlled-substances.update',
        'destroy' => 'controlled-substances.destroy',
    ]);
    Route::get('controlled-substances/{substance}/movements', 'App\Http\Controllers\ControlledSubstanceLogController@index')
        ->name('controlled-substance-logs.index');
    Route::post('controlled-substances/{substance}/movements', 'App\Http\Controllers\ControlledSubstanceLogController@store')
        ->name('controlled-substance-logs.store');
    Route::get('controlled-substances/logs/{controlledSubstanceLog}', 'App\Http\Controllers\ControlledSubstanceLogController@show')
        ->name('controlled-substance-logs.show');
    Route::delete('controlled-substances/logs/{controlledSubstanceLog}', 'App\Http\Controllers\ControlledSubstanceLogController@destroy')
        ->name('controlled-substance-logs.destroy');

    // Anesthesia Monitoring
    Route::resource('anesthesia-monitorings', 'App\Http\Controllers\AnesthesiaMonitoringController')->names([
        'index' => 'anesthesia-monitorings.index',
        'create' => 'anesthesia-monitorings.create',
        'store' => 'anesthesia-monitorings.store',
        'show' => 'anesthesia-monitorings.show',
        'edit' => 'anesthesia-monitorings.edit',
        'update' => 'anesthesia-monitorings.update',
        'destroy' => 'anesthesia-monitorings.destroy',
    ]);

    // Laboratory Orders
    Route::resource('laboratory-orders', 'App\Http\Controllers\LaboratoryOrderController')->names([
        'index' => 'laboratory-orders.index',
        'create' => 'laboratory-orders.create',
        'store' => 'laboratory-orders.store',
        'show' => 'laboratory-orders.show',
        'edit' => 'laboratory-orders.edit',
        'update' => 'laboratory-orders.update',
        'destroy' => 'laboratory-orders.destroy',
    ]);

    // Imaging Exams
    Route::resource('imaging-exams', 'App\Http\Controllers\ImagingExamController')->names([
        'index' => 'imaging-exams.index',
        'create' => 'imaging-exams.create',
        'store' => 'imaging-exams.store',
        'show' => 'imaging-exams.show',
        'edit' => 'imaging-exams.edit',
        'update' => 'imaging-exams.update',
        'destroy' => 'imaging-exams.destroy',
    ]);

    // Referrals
    Route::resource('referrals', 'App\Http\Controllers\ReferralController')->names([
        'index' => 'referrals.index',
        'create' => 'referrals.create',
        'store' => 'referrals.store',
        'show' => 'referrals.show',
        'edit' => 'referrals.edit',
        'update' => 'referrals.update',
        'destroy' => 'referrals.destroy',
    ]);

    // Communication Templates
    Route::resource('communication-templates', 'App\Http\Controllers\CommunicationTemplateController')->names([
        'index' => 'communication-templates.index',
        'create' => 'communication-templates.create',
        'store' => 'communication-templates.store',
        'show' => 'communication-templates.show',
        'edit' => 'communication-templates.edit',
        'update' => 'communication-templates.update',
        'destroy' => 'communication-templates.destroy',
    ]);

    // Communication Queue
    Route::get('communication-queues', 'App\Http\Controllers\CommunicationQueueController@index')->name('communication-queues.index');
    Route::get('communication-queues/create', 'App\Http\Controllers\CommunicationQueueController@create')->name('communication-queues.create');
    Route::post('communication-queues', 'App\Http\Controllers\CommunicationQueueController@store')->name('communication-queues.store');
    Route::get('communication-queues/{communicationQueue}', 'App\Http\Controllers\CommunicationQueueController@show')->name('communication-queues.show');
    Route::delete('communication-queues/{communicationQueue}', 'App\Http\Controllers\CommunicationQueueController@destroy')->name('communication-queues.destroy');

    // Parasite Controls
    Route::resource('parasite-controls', 'App\Http\Controllers\ParasiteControlController')->names([
        'index' => 'parasite-controls.index',
        'create' => 'parasite-controls.create',
        'store' => 'parasite-controls.store',
        'show' => 'parasite-controls.show',
        'edit' => 'parasite-controls.edit',
        'update' => 'parasite-controls.update',
        'destroy' => 'parasite-controls.destroy',
    ]);

    // Vaccine Protocols
    Route::resource('vaccine-protocols', 'App\Http\Controllers\VaccineProtocolController')->names([
        'index' => 'vaccine-protocols.index',
        'create' => 'vaccine-protocols.create',
        'store' => 'vaccine-protocols.store',
        'show' => 'vaccine-protocols.show',
        'edit' => 'vaccine-protocols.edit',
        'update' => 'vaccine-protocols.update',
        'destroy' => 'vaccine-protocols.destroy',
    ]);

    // Teleconsultations
    Route::resource('teleconsultations', 'App\Http\Controllers\TeleconsultationController')->names([
        'index' => 'teleconsultations.index',
        'create' => 'teleconsultations.create',
        'store' => 'teleconsultations.store',
        'show' => 'teleconsultations.show',
        'edit' => 'teleconsultations.edit',
        'update' => 'teleconsultations.update',
        'destroy' => 'teleconsultations.destroy',
    ]);
    Route::get('teleconsultations/{teleconsultation}/start', 'App\Http\Controllers\TeleconsultationController@start')->name('teleconsultations.start');
    Route::post('teleconsultations/{teleconsultation}/end', 'App\Http\Controllers\TeleconsultationController@end')->name('teleconsultations.end');
    Route::get('teleconsultation/{token}', 'App\Http\Controllers\TeleconsultationController@room')->name('teleconsultations.room');

    // Branches (Multi-unit)
    Route::resource('branches', 'App\Http\Controllers\BranchController')->names([
        'index' => 'branches.index',
        'create' => 'branches.create',
        'store' => 'branches.store',
        'show' => 'branches.show',
        'edit' => 'branches.edit',
        'update' => 'branches.update',
        'destroy' => 'branches.destroy',
    ]);

    // Payment Gateways
    Route::resource('payment-gateways', 'App\Http\Controllers\PaymentGatewayController')->names([
        'index' => 'payment-gateways.index',
        'create' => 'payment-gateways.create',
        'store' => 'payment-gateways.store',
        'show' => 'payment-gateways.show',
        'edit' => 'payment-gateways.edit',
        'update' => 'payment-gateways.update',
        'destroy' => 'payment-gateways.destroy',
    ]);

    // Lab Equipment Integrations
    Route::resource('lab-equipment-integrations', 'App\Http\Controllers\LabEquipmentIntegrationController')->names([
        'index' => 'lab-equipment-integrations.index',
        'create' => 'lab-equipment-integrations.create',
        'store' => 'lab-equipment-integrations.store',
        'show' => 'lab-equipment-integrations.show',
        'edit' => 'lab-equipment-integrations.edit',
        'update' => 'lab-equipment-integrations.update',
        'destroy' => 'lab-equipment-integrations.destroy',
    ]);

    // Online Bookings
    Route::resource('online-bookings', 'App\Http\Controllers\OnlineBookingController')->names([
        'index' => 'online-bookings.index',
        'show' => 'online-bookings.show',
        'destroy' => 'online-bookings.destroy',
    ])->only(['index', 'show', 'destroy']);
    Route::post('online-bookings/{onlineBooking}/confirm', 'App\Http\Controllers\OnlineBookingController@confirm')->name('online-bookings.confirm');
    Route::post('online-bookings/{onlineBooking}/reject', 'App\Http\Controllers\OnlineBookingController@reject')->name('online-bookings.reject');

    // Staff Notes
    Route::resource('staff-notes', 'App\Http\Controllers\StaffNoteController')->names([
        'index' => 'staff-notes.index',
        'create' => 'staff-notes.create',
        'store' => 'staff-notes.store',
        'show' => 'staff-notes.show',
        'edit' => 'staff-notes.edit',
        'update' => 'staff-notes.update',
        'destroy' => 'staff-notes.destroy',
    ]);
    Route::post('staff-notes/{staffNote}/mark-read', 'App\Http\Controllers\StaffNoteController@markRead')->name('staff-notes.mark-read');

    // Chat
    Route::get('chat', function () { return view('chat.index'); })->name('chat.index')->middleware('can:chat');

    // Mobile
    Route::prefix('m')->name('mobile.')->group(function () {
        Route::get('/', function () { return view('mobile.index'); })->name('index');
        Route::get('/triage', function () { return view('mobile.triage'); })->name('triage');
        Route::get('/prescriptions', function () { return view('mobile.prescriptions'); })->name('prescriptions');
        Route::get('/records', function () { return view('mobile.records'); })->name('records');
    });

    // Purchase Orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', 'App\Http\Controllers\PurchaseOrderController@index')->name('index');
        Route::get('/create', 'App\Http\Controllers\PurchaseOrderController@create')->name('create');
        Route::post('/', 'App\Http\Controllers\PurchaseOrderController@store')->name('store');
        Route::get('/{purchaseOrder}', 'App\Http\Controllers\PurchaseOrderController@show')->name('show');
        Route::get('/{purchaseOrder}/edit', 'App\Http\Controllers\PurchaseOrderController@edit')->name('edit');
        Route::put('/{purchaseOrder}', 'App\Http\Controllers\PurchaseOrderController@update')->name('update');
        Route::delete('/{purchaseOrder}', 'App\Http\Controllers\PurchaseOrderController@destroy')->name('destroy');
        Route::post('/{purchaseOrder}/order', 'App\Http\Controllers\PurchaseOrderController@order')->name('order');
        Route::post('/{purchaseOrder}/receive', 'App\Http\Controllers\PurchaseOrderController@receive')->name('receive');
    });

    // Scanner
    Route::get('scanner', function () { return view('scanner'); })->name('scanner.index')->middleware('can:products.view');

    // Kennel Map
    Route::get('boardings/kennel-map', 'App\Http\Controllers\BoardingController@kennelMap')->name('boardings.kennel-map');
    Route::post('boardings/{boarding}/assign-kennel', 'App\Http\Controllers\BoardingController@assignKennel')->name('boardings.assign-kennel');

    // Pet Death Records
    Route::resource('pet-death-records', 'App\Http\Controllers\PetDeathRecordController')->names([
        'index' => 'pet-death-records.index',
        'create' => 'pet-death-records.create',
        'store' => 'pet-death-records.store',
        'show' => 'pet-death-records.show',
        'edit' => 'pet-death-records.edit',
        'update' => 'pet-death-records.update',
        'destroy' => 'pet-death-records.destroy',
    ]);

    // Grooming Templates
    Route::resource('grooming-templates', 'App\Http\Controllers\GroomingTemplateController')->names([
        'index' => 'grooming-templates.index',
        'create' => 'grooming-templates.create',
        'store' => 'grooming-templates.store',
        'show' => 'grooming-templates.show',
        'edit' => 'grooming-templates.edit',
        'update' => 'grooming-templates.update',
        'destroy' => 'grooming-templates.destroy',
    ]);

    // Therapy Sessions
    Route::resource('therapy-sessions', 'App\Http\Controllers\TherapySessionController')->names([
        'index' => 'therapy-sessions.index',
        'create' => 'therapy-sessions.create',
        'store' => 'therapy-sessions.store',
        'show' => 'therapy-sessions.show',
        'edit' => 'therapy-sessions.edit',
        'update' => 'therapy-sessions.update',
        'destroy' => 'therapy-sessions.destroy',
    ]);

    // Breed Defaults
    Route::resource('breed-defaults', 'App\Http\Controllers\BreedDefaultController')->names([
        'index' => 'breed-defaults.index',
        'create' => 'breed-defaults.create',
        'store' => 'breed-defaults.store',
        'show' => 'breed-defaults.show',
        'edit' => 'breed-defaults.edit',
        'update' => 'breed-defaults.update',
        'destroy' => 'breed-defaults.destroy',
    ]);

    // Vaccination Certificate
    Route::get('vaccinations/{pet}/certificate', 'App\Http\Controllers\VaccinationController@certificate')->name('vaccinations.certificate');

    // Drug Formulary
    Route::resource('drug-formulary', 'App\Http\Controllers\DrugFormularyController')->names([
        'index' => 'drug-formulary.index',
        'create' => 'drug-formulary.create',
        'store' => 'drug-formulary.store',
        'edit' => 'drug-formulary.edit',
        'update' => 'drug-formulary.update',
        'destroy' => 'drug-formulary.destroy',
    ]);
    Route::post('drug-formulary/calculate', 'App\Http\Controllers\DrugFormularyController@calculate')->name('drug-formulary.calculate');

    // Tutor Communication History
    Route::get('tutors/{tutor}/communication', 'App\Http\Controllers\TutorController@communication')->name('tutors.communication');

    // Prescription Print
    Route::get('prescriptions/{prescription}/print', 'App\Http\Controllers\PrescriptionController@print')->name('prescriptions.print');

    // Staff Schedules (on-call-calendar must be before resource to avoid matching show route)
    Route::get('staff-schedules/on-call-calendar', 'App\Http\Controllers\StaffScheduleController@onCallCalendar')->name('staff-schedules.on-call-calendar');
    Route::get('staff-schedules/vet-shifts', 'App\Http\Controllers\StaffScheduleController@vetShifts')->name('staff-schedules.vet-shifts');
    Route::resource('staff-schedules', 'App\Http\Controllers\StaffScheduleController')->names([
        'index' => 'staff-schedules.index',
        'create' => 'staff-schedules.create',
        'store' => 'staff-schedules.store',
        'edit' => 'staff-schedules.edit',
        'update' => 'staff-schedules.update',
        'destroy' => 'staff-schedules.destroy',
    ]);
    Route::get('staff-time-off', 'App\Http\Controllers\StaffScheduleController@timeOff')->name('staff-schedules.time-off');
    Route::post('staff-time-off', 'App\Http\Controllers\StaffScheduleController@storeTimeOff')->name('staff-schedules.time-off.store');
    Route::post('staff-time-off/{staffTimeOff}/approve', 'App\Http\Controllers\StaffScheduleController@approveTimeOff')->name('staff-time-off.approve');
    Route::post('staff-time-off/{staffTimeOff}/reject', 'App\Http\Controllers\StaffScheduleController@rejectTimeOff')->name('staff-time-off.reject');

    // Audit Logs
    Route::resource('audit-logs', 'App\Http\Controllers\AuditLogController')->names([
        'index' => 'audit-logs.index',
        'show' => 'audit-logs.show',
    ])->only(['index', 'show']);

    // ANVISA Reports
    Route::get('controlled-substances/reports/monthly', 'App\Http\Controllers\ControlledSubstanceController@reportMonthly')->name('controlled-substances.reports.monthly');
    Route::get('controlled-substances/reports/annual', 'App\Http\Controllers\ControlledSubstanceController@reportAnnual')->name('controlled-substances.reports.annual');
    Route::get('controlled-substances/reports/export-csv', 'App\Http\Controllers\ControlledSubstanceController@exportCsv')->name('controlled-substances.reports.export-csv');

    // Database Backups
    Route::resource('backups', 'App\Http\Controllers\BackupController')->names([
        'index' => 'backups.index',
        'create' => 'backups.create',
        'destroy' => 'backups.destroy',
        'show' => 'backups.show',
    ])->only(['index', 'create', 'destroy']);
    Route::get('backups/{filename}/download', 'App\Http\Controllers\BackupController@download')->name('backups.download');

    // Boarding / Grooming
    Route::resource('boardings', 'App\Http\Controllers\BoardingController')->names([
        'index' => 'boardings.index',
        'create' => 'boardings.create',
        'store' => 'boardings.store',
        'show' => 'boardings.show',
        'edit' => 'boardings.edit',
        'update' => 'boardings.update',
        'destroy' => 'boardings.destroy',
    ]);
    Route::post('boardings/{boarding}/checkout', 'App\Http\Controllers\BoardingController@checkout')->name('boardings.checkout');
    Route::post('boardings/{boarding}/cancel', 'App\Http\Controllers\BoardingController@cancel')->name('boardings.cancel');
    Route::post('boardings/{boarding}/tasks', 'App\Http\Controllers\BoardingController@storeTask')->name('boardings.tasks.store');
    Route::post('boardings/{boarding}/tasks/{task}/complete', 'App\Http\Controllers\BoardingController@completeTask')->name('boardings.tasks.complete');
    Route::delete('boardings/{boarding}/tasks/{task}', 'App\Http\Controllers\BoardingController@destroyTask')->name('boardings.tasks.destroy');
    Route::get('boardings-active', 'App\Http\Controllers\BoardingController@active')->name('boardings.active');

    // Drug Interactions
    Route::resource('drug-interactions', 'App\Http\Controllers\DrugInteractionController')->names([
        'index' => 'drug-interactions.index',
        'create' => 'drug-interactions.create',
        'store' => 'drug-interactions.store',
        'show' => 'drug-interactions.show',
        'edit' => 'drug-interactions.edit',
        'update' => 'drug-interactions.update',
        'destroy' => 'drug-interactions.destroy',
    ]);
    Route::post('drug-interactions/check', 'App\Http\Controllers\DrugInteractionController@checkApi')->name('drug-interactions.check');

    // Clinical Report Templates
    Route::resource('clinical-report-templates', 'App\Http\Controllers\ClinicalReportTemplateController')->names([
        'index' => 'clinical-report-templates.index',
        'create' => 'clinical-report-templates.create',
        'store' => 'clinical-report-templates.store',
        'show' => 'clinical-report-templates.show',
        'edit' => 'clinical-report-templates.edit',
        'update' => 'clinical-report-templates.update',
        'destroy' => 'clinical-report-templates.destroy',
    ]);

    // Health Certificates
    Route::resource('health-certificates', 'App\Http\Controllers\HealthCertificateController')->names([
        'index' => 'health-certificates.index',
        'create' => 'health-certificates.create',
        'store' => 'health-certificates.store',
        'show' => 'health-certificates.show',
        'edit' => 'health-certificates.edit',
        'update' => 'health-certificates.update',
        'destroy' => 'health-certificates.destroy',
    ]);
    Route::get('health-certificates/{healthCertificate}/pdf', 'App\Http\Controllers\HealthCertificateController@pdf')->name('health-certificates.pdf');
    Route::get('health-certificates/{healthCertificate}/cvi-pdf', 'App\Http\Controllers\HealthCertificateController@downloadCviPdf')->name('health-certificates.cvi-pdf');

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

    // HR - Departments
    Route::resource('departments', 'App\Http\Controllers\DepartmentController')->names([
        'index' => 'departments.index',
        'create' => 'departments.create',
        'store' => 'departments.store',
        'show' => 'departments.show',
        'edit' => 'departments.edit',
        'update' => 'departments.update',
        'destroy' => 'departments.destroy',
    ]);

    // HR - Positions
    Route::resource('positions', 'App\Http\Controllers\PositionController')->names([
        'index' => 'positions.index',
        'create' => 'positions.create',
        'store' => 'positions.store',
        'show' => 'positions.show',
        'edit' => 'positions.edit',
        'update' => 'positions.update',
        'destroy' => 'positions.destroy',
    ]);

    // HR - Employees (view-only)
    Route::resource('employees', 'App\Http\Controllers\EmployeeController')->names([
        'index' => 'employees.index',
        'show' => 'employees.show',
    ])->only(['index', 'show']);

    // Pre-Anesthetic Evaluations
    Route::resource('pre-anesthetic-evaluations', 'App\Http\Controllers\PreAnestheticEvaluationController')->names([
        'index' => 'pre-anesthetic-evaluations.index',
        'create' => 'pre-anesthetic-evaluations.create',
        'store' => 'pre-anesthetic-evaluations.store',
        'show' => 'pre-anesthetic-evaluations.show',
        'edit' => 'pre-anesthetic-evaluations.edit',
        'update' => 'pre-anesthetic-evaluations.update',
        'destroy' => 'pre-anesthetic-evaluations.destroy',
    ]);

    // Diet Plans
    Route::resource('diet-plans', 'App\Http\Controllers\DietPlanController')->names([
        'index' => 'diet-plans.index',
        'create' => 'diet-plans.create',
        'store' => 'diet-plans.store',
        'show' => 'diet-plans.show',
        'edit' => 'diet-plans.edit',
        'update' => 'diet-plans.update',
        'destroy' => 'diet-plans.destroy',
    ]);

    // Convenio Claims
    Route::resource('convenio-claims', 'App\Http\Controllers\ConvenioClaimController')->names([
        'index' => 'convenio-claims.index',
        'create' => 'convenio-claims.create',
        'store' => 'convenio-claims.store',
        'show' => 'convenio-claims.show',
        'edit' => 'convenio-claims.edit',
        'update' => 'convenio-claims.update',
        'destroy' => 'convenio-claims.destroy',
    ]);

    // Triage
    Route::resource('triage', 'App\Http\Controllers\TriageRecordController')->names([
        'index' => 'triage.index',
        'create' => 'triage.create',
        'store' => 'triage.store',
        'show' => 'triage.show',
        'edit' => 'triage.edit',
        'update' => 'triage.update',
        'destroy' => 'triage.destroy',
    ]);

    // Emergency Protocols
    Route::resource('emergency-protocols', 'App\Http\Controllers\EmergencyProtocolController')->names([
        'index' => 'emergency-protocols.index',
        'create' => 'emergency-protocols.create',
        'store' => 'emergency-protocols.store',
        'show' => 'emergency-protocols.show',
        'edit' => 'emergency-protocols.edit',
        'update' => 'emergency-protocols.update',
        'destroy' => 'emergency-protocols.destroy',
    ]);

    // Corporate Dashboard
    Route::get('corporate-dashboard', 'App\Http\Controllers\CorporateDashboardController@index')->name('corporate-dashboard.index');

    // System Update
    Route::get('system-update', 'App\Http\Controllers\SystemUpdateController@index')->name('system-update.index');
    Route::post('system-update/token', 'App\Http\Controllers\SystemUpdateController@token')->name('system-update.token');
    Route::get('system-update/check', 'App\Http\Controllers\SystemUpdateController@check')->name('system-update.check');
    Route::post('system-update/apply', 'App\Http\Controllers\SystemUpdateController@apply')->name('system-update.apply');
    Route::get('system-update/history', 'App\Http\Controllers\SystemUpdateController@history')->name('system-update.history');

    // Documentation
    Route::get('docs', 'App\Http\Controllers\DocController@index')->name('docs.index');
    Route::get('docs/{section}', 'App\Http\Controllers\DocController@show')->name('docs.show');
    Route::get('docs/{section}/{page}', 'App\Http\Controllers\DocController@show')->name('docs.page');


    // LLM / IA Diagnóstica
    Route::get('llm/config', 'App\Http\Controllers\LlmConfigController@edit')->name('llm.config')->middleware('can:configuracoes.llm');
    Route::put('llm/config', 'App\Http\Controllers\LlmConfigController@update')->name('llm.config.update')->middleware('can:configuracoes.llm');

    // Branding / Personalização
    Route::prefix('configuracoes')->group(function () {
        Route::get('branding', 'App\Http\Controllers\BrandingController@index')->name('configuracoes.branding.index');
        Route::put('branding', 'App\Http\Controllers\BrandingController@update')->name('configuracoes.branding.update');

        Route::get('notificacoes', 'App\Http\Controllers\NotificationConfigController@index')->name('configuracoes.notificacoes.index');
        Route::put('notificacoes', 'App\Http\Controllers\NotificationConfigController@update')->name('configuracoes.notificacoes.update');
    });
});

// Digital signature verification (public, rate-limited)
Route::get('verify/{model}/{id}', 'App\Http\Controllers\SignatureVerifyController@verify')
    ->name('signature.verify')
    ->middleware('throttle:30,1');

// Insurance claim webhook (external callback, no auth)
Route::post('api/insurance/webhook', 'App\Http\Controllers\InsuranceWebhookController')
    ->name('insurance.webhook')
    ->middleware('throttle:60,1');

