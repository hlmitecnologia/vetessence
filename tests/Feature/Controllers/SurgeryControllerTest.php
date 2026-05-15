<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Surgery;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class SurgeryControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Surgery::factory()->count(3)->create();
        $response = $this->get(route('surgeries.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('surgeries.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('surgeries.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'scheduled_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'surgery_type' => 'Castração',
            'status' => 'scheduled',
        ]);
        $response->assertRedirect(route('surgeries.index'));
        $this->assertDatabaseHas('surgeries', ['pet_id' => $pet->id, 'surgery_type' => 'Castração']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('surgeries.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'vet_id', 'scheduled_date', 'surgery_type']);
    }

    public function test_show()
    {
        $surgery = Surgery::factory()->create();
        $response = $this->get(route('surgeries.show', $surgery));
        $response->assertOk();
    }

    public function test_edit()
    {
        $surgery = Surgery::factory()->create();
        $response = $this->get(route('surgeries.edit', $surgery));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $surgery = Surgery::factory()->create(['scheduled_date' => now()->addDays(7)]);

        $response = $this->put(route('surgeries.update', $surgery), [
            'scheduled_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'surgery_type' => 'Ortopedia',
            'status' => 'scheduled',
        ]);
        $response->assertRedirect(route('surgeries.show', $surgery));
        $this->assertDatabaseHas('surgeries', ['id' => $surgery->id, 'surgery_type' => 'Ortopedia']);
    }

    public function test_destroy_deletes_record()
    {
        $surgery = Surgery::factory()->create(['status' => 'scheduled']);
        $response = $this->delete(route('surgeries.destroy', $surgery));
        $response->assertRedirect(route('surgeries.index'));
        $this->assertDatabaseMissing('surgeries', ['id' => $surgery->id]);
    }

    public function test_destroy_fails_when_in_progress()
    {
        $surgery = Surgery::factory()->create(['status' => 'in_progress']);
        $response = $this->delete(route('surgeries.destroy', $surgery));
        $response->assertRedirect();
        $this->assertDatabaseHas('surgeries', ['id' => $surgery->id]);
    }
}
