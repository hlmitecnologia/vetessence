<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\AuditObserver;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use App\Models\Product;
use App\Models\User;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Invoice::observe(AuditObserver::class);
        Appointment::observe(AuditObserver::class);
        Pet::observe(AuditObserver::class);
        Tutor::observe(AuditObserver::class);
        MedicalRecord::observe(AuditObserver::class);
        Vaccination::observe(AuditObserver::class);
        Product::observe(AuditObserver::class);
        User::observe(AuditObserver::class);
    }
}
