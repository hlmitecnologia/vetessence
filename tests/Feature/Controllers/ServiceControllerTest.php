<?php

namespace Tests\Feature\Controllers;

use App\Models\Service;
use Tests\ModuleTestCase;

class ServiceControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Service::factory()->count(3)->create();
        $response = $this->get(route('services.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('services.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('services.store'), [
            'name' => 'Consulta Geral',
            'price' => 150.00,
            'description' => 'Atendimento clínico geral',
        ]);
        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', ['name' => 'Consulta Geral']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('services.store'), ['name' => '']);
        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_show()
    {
        $service = Service::factory()->create();
        $response = $this->get(route('services.show', $service));
        $response->assertOk();
    }

    public function test_edit()
    {
        $service = Service::factory()->create();
        $response = $this->get(route('services.edit', $service));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $service = Service::factory()->create(['name' => 'Antigo']);
        $response = $this->put(route('services.update', $service), [
            'name' => 'Consulta Especializada',
            'price' => 200.00,
        ]);
        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', ['id' => $service->id, 'name' => 'Consulta Especializada']);
    }

    public function test_destroy_deletes_record()
    {
        $service = Service::factory()->create();
        $response = $this->delete(route('services.destroy', $service));
        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
