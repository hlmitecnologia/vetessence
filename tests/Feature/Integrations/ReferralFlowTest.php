<?php

namespace Tests\Feature\Integrations;

use App\Models\Branch;
use App\Models\Pet;
use App\Models\Referral;
use App\Models\User;
use Tests\ModuleTestCase;

class ReferralFlowTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $referringVet;
    protected User $receivingVet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->referringVet = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->receivingVet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_full_referral_lifecycle()
    {
        $pet = Pet::factory()->create();

        $createResponse = $this->post(route('referrals.store'), [
            'pet_id' => $pet->id,
            'referring_vet_id' => $this->referringVet->id,
            'receiving_vet_id' => $this->receivingVet->id,
            'reason' => 'Paciente necessita avaliação ortopédica especializada',
            'clinical_history' => 'Claudicação em membro anterior direito há 2 semanas',
            'status' => 'pending',
            'branch_id' => $this->branch->id,
        ]);
        $createResponse->assertSessionDoesntHaveErrors();
        $createResponse->assertRedirect();

        $this->assertDatabaseHas('referrals', [
            'pet_id' => $pet->id,
            'status' => 'pending',
            'reason' => 'Paciente necessita avaliação ortopédica especializada',
        ]);
        $referral = Referral::where('pet_id', $pet->id)->first();

        $updateResponse = $this->put(route('referrals.update', $referral), [
            'pet_id' => $pet->id,
            'referring_vet_id' => $this->referringVet->id,
            'receiving_vet_id' => $this->receivingVet->id,
            'reason' => 'Paciente necessita avaliação ortopédica especializada',
            'status' => 'responded',
            'response_notes' => 'Confirmado atendimento. Exames solicitados.',
            'branch_id' => $this->branch->id,
        ]);
        $updateResponse->assertSessionDoesntHaveErrors();
        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('referrals', [
            'id' => $referral->id,
            'status' => 'responded',
        ]);

        $completeResponse = $this->put(route('referrals.update', $referral), [
            'pet_id' => $pet->id,
            'referring_vet_id' => $this->referringVet->id,
            'receiving_vet_id' => $this->receivingVet->id,
            'reason' => 'Paciente necessita avaliação ortopédica especializada',
            'status' => 'completed',
            'response_notes' => 'Confirmado atendimento. Exames solicitados.',
            'completed_at' => now()->format('Y-m-d'),
            'branch_id' => $this->branch->id,
        ]);
        $completeResponse->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('referrals', [
            'id' => $referral->id,
            'status' => 'completed',
        ]);
    }
}
