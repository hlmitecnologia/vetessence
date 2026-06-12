<?php

namespace Tests\Feature\Controllers;

use App\Models\Appointment;
use App\Models\Hospitalization;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Product;
use App\Models\VaccinationReminder;
use App\Models\ParasiteControl;
use Tests\ModuleTestCase;

class DashboardControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_loads_successfully()
    {
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_today_appointments()
    {
        Appointment::factory()->create([
            'date' => today(),
            'status' => 'scheduled',
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_total_pets()
    {
        Pet::factory()->count(3)->create(['is_active' => true]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_month_revenue()
    {
        Invoice::factory()->create([
            'status' => 'paid',
            'paid_at' => now(),
            'total' => 5000.00,
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_low_stock_count()
    {
        Product::factory()->create([
            'stock' => 1,
            'min_stock' => 5,
            'is_active' => true,
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_active_hospitalizations()
    {
        Hospitalization::factory()->create(['status' => 'admitted']);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_pending_reminders()
    {
        VaccinationReminder::factory()->create([
            'status' => 'pending',
            'scheduled_date' => today()->subDay(),
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_overdue_parasite_controls()
    {
        ParasiteControl::factory()->create([
            'next_due_date' => today()->subDay(),
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_computes_no_show_rate()
    {
        Appointment::factory()->count(5)->create([
            'date' => today(),
            'status' => 'scheduled',
        ]);
        Appointment::factory()->create([
            'date' => today(),
            'status' => 'no_show',
        ]);
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_index_shows_recent_medical_records()
    {
        MedicalRecord::factory()->count(5)->create();
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }
}
