<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\ConsentForm;
use App\Models\Hospitalization;
use App\Models\ImagingExam;
use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\Referral;
use App\Models\TreatmentPlan;
use App\Models\Tutor;
use App\Models\User;
use App\Models\WeightRecord;
use Tests\ModuleTestCase;

class ModuleApiControllerTest extends ModuleTestCase
{
    private Pet $pet;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->loginAs('admin');
        $this->pet = Pet::factory()->create();
    }

    public function test_weight_records_returns_paginated_records()
    {
        WeightRecord::factory()->count(3)->create(['pet_id' => $this->pet->id]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/weight-records');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'current_page', 'total']);
        $response->assertJsonCount(3, 'data');
    }

    public function test_weight_records_filters_by_pet()
    {
        $otherPet = Pet::factory()->create();
        WeightRecord::factory()->create(['pet_id' => $this->pet->id]);
        WeightRecord::factory()->create(['pet_id' => $otherPet->id]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/weight-records?pet_id=' . $this->pet->id);

        $response->assertJsonCount(1, 'data');
    }

    public function test_hospitalizations_returns_paginated()
    {
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        Hospitalization::factory()->count(3)->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/hospitalizations');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_hospitalizations_filters_by_status()
    {
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        Hospitalization::factory()->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'admitted',
        ]);
        Hospitalization::factory()->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'discharged',
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/hospitalizations?status=admitted');

        $response->assertJsonCount(1, 'data');
    }

    public function test_treatment_plans_returns_paginated()
    {
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        TreatmentPlan::factory()->count(2)->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/treatment-plans');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_treatment_plans_filters_by_status()
    {
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        TreatmentPlan::factory()->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'active',
        ]);
        TreatmentPlan::factory()->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/treatment-plans?status=active');

        $response->assertJsonCount(1, 'data');
    }

    public function test_laboratory_orders_returns_paginated()
    {
        $vet = User::factory()->create();
        LaboratoryOrder::factory()->count(2)->create([
            'pet_id' => $this->pet->id,
            'vet_id' => $vet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/laboratory-orders');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_imaging_exams_returns_paginated()
    {
        $vet = User::factory()->create();
        ImagingExam::factory()->count(2)->create([
            'pet_id' => $this->pet->id,
            'vet_id' => $vet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/imaging-exams');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_referrals_returns_paginated()
    {
        $referringVet = User::factory()->create();
        $receivingVet = User::factory()->create();
        Referral::factory()->count(2)->create([
            'pet_id' => $this->pet->id,
            'referring_vet_id' => $referringVet->id,
            'receiving_vet_id' => $receivingVet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/referrals');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_consent_forms_returns_paginated()
    {
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        ConsentForm::factory()->count(2)->create([
            'pet_id' => $this->pet->id,
            'tutor_id' => $tutor->id,
            'veterinarian_id' => $vet->id,
        ]);

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/consent-forms');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_endpoints_return_empty_when_pet_has_no_data()
    {
        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/weight-records');
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/hospitalizations');
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/treatment-plans');
        $response->assertJsonCount(0, 'data');
    }

    public function test_endpoints_require_authentication()
    {
        $this->app->get('auth')->forgetGuards();

        $response = $this->getJson('/api/v1/my-pets/' . $this->pet->id . '/weight-records');
        $response->assertUnauthorized();
    }
}
