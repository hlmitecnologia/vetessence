<?php

namespace Tests\Unit\Models;

use App\Models\Referral;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Referral::create([
            'referral_number' => 'REF-20260515-0001', 'pet_id' => $pet->id,
            'referring_vet_id' => $vet->id, 'reason' => 'Especialista', 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('referrals', ['referral_number' => 'REF-20260515-0001', 'status' => 'pending']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $ref = Referral::create(['referral_number' => 'REF-001', 'pet_id' => $pet->id, 'reason' => 'teste', 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $ref->pet);
    }

    public function test_referring_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $ref = Referral::create(['referral_number' => 'REF-002', 'pet_id' => $pet->id, 'referring_vet_id' => $vet->id, 'reason' => 'teste', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $ref->referringVet);
    }

    public function test_receiving_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet1 = User::factory()->create();
        $vet2 = User::factory()->create();
        $ref = Referral::create(['referral_number' => 'REF-003', 'pet_id' => $pet->id, 'referring_vet_id' => $vet1->id, 'receiving_vet_id' => $vet2->id, 'reason' => 'teste', 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $ref->receivingVet);
    }
}
