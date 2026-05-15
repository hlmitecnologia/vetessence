<?php

namespace Tests\Feature\Controllers;

use App\Models\Convenio;
use Tests\ModuleTestCase;

class ConvenioControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Convenio::factory()->count(3)->create();
        $response = $this->get(route('convenios.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('convenios.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('convenios.store'), [
            'name' => 'Plano Saúde Pet',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('convenios.index'));
        $this->assertDatabaseHas('convenios', ['name' => 'Plano Saúde Pet']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('convenios.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $convenio = Convenio::factory()->create();
        $response = $this->get(route('convenios.show', $convenio));
        $response->assertOk();
    }

    public function test_edit()
    {
        $convenio = Convenio::factory()->create();
        $response = $this->get(route('convenios.edit', $convenio));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $convenio = Convenio::factory()->create(['name' => 'Antigo']);
        $response = $this->put(route('convenios.update', $convenio), [
            'name' => 'Plano VIP',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('convenios.index'));
        $this->assertDatabaseHas('convenios', ['id' => $convenio->id, 'name' => 'Plano VIP']);
    }

    public function test_destroy_deletes_record()
    {
        $convenio = Convenio::factory()->create();
        $response = $this->delete(route('convenios.destroy', $convenio));
        $response->assertRedirect(route('convenios.index'));
        $this->assertDatabaseMissing('convenios', ['id' => $convenio->id]);
    }
}
