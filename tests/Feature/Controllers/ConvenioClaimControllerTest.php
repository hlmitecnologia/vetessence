<?php

namespace Tests\Feature\Controllers;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use Tests\ModuleTestCase;

class ConvenioClaimControllerTest extends ModuleTestCase
{
    public function test_index()
    {
        $this->loginAs('veterinario');
        ConvenioClaim::factory()->count(2)->create();
        $response = $this->get(route('convenio-claims.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $this->loginAs('veterinario');
        $response = $this->get(route('convenio-claims.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $this->loginAs('veterinario');
        $cp = ConvenioPet::factory()->create();
        $response = $this->post(route('convenio-claims.store'), [
            'convenio_pet_id' => $cp->id,
            'amount_requested' => 500.00,
        ]);
        $response->assertRedirect(route('convenio-claims.index'));
        $this->assertDatabaseHas('convenio_claims', ['convenio_pet_id' => $cp->id]);
    }

    public function test_store_validates_required_fields()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('convenio-claims.store'), []);
        $response->assertSessionHasErrors(['convenio_pet_id', 'amount_requested']);
    }

    public function test_show()
    {
        $this->loginAs('veterinario');
        $claim = ConvenioClaim::factory()->create();
        $response = $this->get(route('convenio-claims.show', $claim));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $this->loginAs('veterinario');
        $claim = ConvenioClaim::factory()->create();
        $response = $this->delete(route('convenio-claims.destroy', $claim));
        $response->assertRedirect(route('convenio-claims.index'));
        $this->assertDatabaseMissing('convenio_claims', ['id' => $claim->id]);
    }
}
