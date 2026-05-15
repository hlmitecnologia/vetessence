<?php

namespace Tests\Unit\Models;

use App\Models\ConsentForm;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConsentFormTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        ConsentForm::create([
            'consent_number' => 'CON-20260515-0001', 'pet_id' => $pet->id, 'tutor_id' => $tutor->id,
            'client_name' => 'João', 'veterinarian_id' => $vet->id, 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('consent_forms', ['consent_number' => 'CON-20260515-0001', 'status' => 'pending']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $cf = ConsentForm::create(['consent_number' => 'CON-001', 'pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $cf->pet);
    }

    public function test_tutor_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $cf = ConsentForm::create(['consent_number' => 'CON-002', 'pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'client_name' => 'João', 'status' => 'pending']);
        $this->assertInstanceOf(Tutor::class, $cf->tutor);
    }

    public function test_veterinarian_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $cf = ConsentForm::create(['consent_number' => 'CON-003', 'pet_id' => $pet->id, 'veterinarian_id' => $vet->id, 'client_name' => 'João', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $cf->veterinarian);
    }
}
