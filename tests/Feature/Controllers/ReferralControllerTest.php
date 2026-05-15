<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Referral;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class ReferralControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('referrals.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('referrals.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('referrals.store'), [
            'pet_id' => $pet->id,
            'referring_vet_id' => $vet->id,
            'receiving_vet_id' => $vet->id,
            'reason' => 'Paciente necessita avaliação especializada',
            'status' => 'pending',
        ]);
        $response->assertRedirect(route('referrals.index'));
        $this->assertDatabaseHas('referrals', ['pet_id' => $pet->id]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('referrals.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'referring_vet_id', 'reason', 'status']);
    }

    public function test_show()
    {
        $referral = Referral::factory()->create();
        $response = $this->get(route('referrals.show', $referral));
        $response->assertOk();
    }

    public function test_edit()
    {
        $referral = Referral::factory()->create();
        $response = $this->get(route('referrals.edit', $referral));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $referral = Referral::factory()->create();

        $response = $this->put(route('referrals.update', $referral), [
            'pet_id' => $pet->id,
            'referring_vet_id' => $vet->id,
            'reason' => 'Motivo atualizado',
            'status' => 'approved',
        ]);
        $response->assertRedirect(route('referrals.index'));
        $this->assertDatabaseHas('referrals', ['id' => $referral->id, 'status' => 'approved']);
    }

    public function test_destroy_deletes_record()
    {
        $referral = Referral::factory()->create();
        $response = $this->delete(route('referrals.destroy', $referral));
        $response->assertRedirect(route('referrals.index'));
        $this->assertDatabaseMissing('referrals', ['id' => $referral->id]);
    }
}
