<?php

namespace Tests\Feature\Controllers;

use App\Models\TriageRecord;
use App\Models\Pet;
use Tests\ModuleTestCase;

class TriageRecordControllerTest extends ModuleTestCase
{
    public function test_index()
    {
        $this->loginAs('veterinario');
        TriageRecord::factory()->count(3)->create();
        $response = $this->get(route('triage.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $this->loginAs('veterinario');
        $response = $this->get(route('triage.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $this->loginAs('veterinario');
        $pet = Pet::factory()->create();
        $response = $this->post(route('triage.store'), [
            'pet_id' => $pet->id,
            'severity' => 'yellow',
            'chief_complaint' => 'Vomiting',
        ]);
        $response->assertRedirect(route('triage.index'));
        $this->assertDatabaseHas('triage_records', ['pet_id' => $pet->id]);
    }

    public function test_store_validates_required_fields()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('triage.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'severity', 'chief_complaint']);
    }

    public function test_show()
    {
        $this->loginAs('veterinario');
        $triage = TriageRecord::factory()->create();
        $response = $this->get(route('triage.show', $triage));
        $response->assertOk();
    }

    public function test_update_status()
    {
        $this->loginAs('veterinario');
        $triage = TriageRecord::factory()->create(['status' => 'waiting']);
        $response = $this->put(route('triage.update', $triage), [
            'severity' => 'red',
            'status' => 'in_consultation',
        ]);
        $response->assertRedirect(route('triage.index'));
        $this->assertEquals('in_consultation', $triage->fresh()->status);
    }

    public function test_destroy()
    {
        $this->loginAs('veterinario');
        $triage = TriageRecord::factory()->create();
        $response = $this->delete(route('triage.destroy', $triage));
        $response->assertRedirect(route('triage.index'));
        $this->assertDatabaseMissing('triage_records', ['id' => $triage->id]);
    }
}
