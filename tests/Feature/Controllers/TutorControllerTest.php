<?php

namespace Tests\Feature\Controllers;

use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class TutorControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        User::factory()->create(['email' => 'tutor1@test.com']);
        Tutor::factory()->count(3)->create();
        $response = $this->get(route('tutors.index'));
        $response->assertOk();
    }

    public function test_create_redirects_to_index()
    {
        $response = $this->get(route('tutors.create'));
        $response->assertRedirect(route('tutors.index'));
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('tutors.store'), [
            'name' => 'João da Silva',
            'cpf' => '529.982.247-25',
            'email' => 'joao@example.com',
            'phone' => '11988888888',
            'address' => 'Rua das Flores, 123',
        ]);
        $response->assertRedirect(route('tutors.index'));
        $this->assertDatabaseHas('tutors', ['email' => 'joao@example.com']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('tutors.store'), ['name' => '']);
        $response->assertSessionHasErrors(['name', 'cpf', 'email', 'phone']);
    }

    public function test_show()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->get(route('tutors.show', $tutor));
        $response->assertOk();
    }

    public function test_edit_redirects_to_index()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->get(route('tutors.edit', $tutor));
        $response->assertRedirect(route('tutors.index'));
    }

    public function test_update_modifies_record()
    {
        $tutor = Tutor::factory()->create(['email' => 'old@example.com']);
        $response = $this->put(route('tutors.update', $tutor), [
            'name' => 'Maria Souza',
            'cpf' => $tutor->cpf ?: '529.982.247-25',
            'email' => 'maria@example.com',
            'phone' => '11977777777',
        ]);
        $response->assertRedirect(route('tutors.index'));
        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'email' => 'maria@example.com']);
    }

    public function test_destroy_deletes_record()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->delete(route('tutors.destroy', $tutor));
        $response->assertRedirect(route('tutors.index'));
        $this->assertDatabaseMissing('tutors', ['id' => $tutor->id]);
    }

    public function test_communication()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->get(route('tutors.communication', $tutor));
        $response->assertOk();
    }
}
