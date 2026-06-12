<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class TutorControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_returns_paginated_tutors()
    {
        Tutor::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/tutors');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_index_filters_by_search()
    {
        Tutor::factory()->create(['name' => 'John Doe']);
        Tutor::factory()->create(['name' => 'Jane Smith']);

        $response = $this->getJson('/api/v1/tutors?search=John');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_returns_tutor_with_pets()
    {
        $tutor = Tutor::factory()->create();

        $response = $this->getJson('/api/v1/tutors/' . $tutor->id);

        $response->assertOk()
            ->assertJsonStructure(['id', 'name', 'cpf', 'phone', 'email', 'address', 'city', 'state', 'pets']);
    }

    public function test_show_returns_404_for_nonexistent_tutor()
    {
        $response = $this->getJson('/api/v1/tutors/99999');

        $response->assertNotFound();
    }

    public function test_store_creates_tutor()
    {
        $cpf = $this->validCpf();

        $response = $this->postJson('/api/v1/tutors', [
            'name' => 'Maria Silva',
            'cpf' => $cpf,
            'email' => 'maria@example.com',
            'phone' => '11988888888',
        ]);

        $response->assertCreated()
            ->assertJson(['name' => 'Maria Silva']);

        $this->assertDatabaseHas('tutors', ['cpf' => $cpf]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/tutors', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'cpf', 'email', 'phone']);
    }

    public function test_store_validates_unique_cpf()
    {
        $cpf = $this->validCpf();
        Tutor::factory()->create(['cpf' => $cpf]);

        $response = $this->postJson('/api/v1/tutors', [
            'name' => 'Outro Tutor',
            'cpf' => $cpf,
            'email' => 'outro@example.com',
            'phone' => '11977777777',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_update_modifies_tutor()
    {
        $tutor = Tutor::factory()->create();

        $response = $this->putJson('/api/v1/tutors/' . $tutor->id, [
            'name' => 'Updated Name',
            'phone' => '11966666666',
        ]);

        $response->assertOk()
            ->assertJson(['name' => 'Updated Name']);

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Updated Name']);
    }

    public function test_update_validates_unique_email()
    {
        $tutor1 = Tutor::factory()->create(['email' => 'tutor1@example.com']);
        $tutor2 = Tutor::factory()->create(['email' => 'tutor2@example.com']);

        $response = $this->putJson('/api/v1/tutors/' . $tutor2->id, [
            'email' => 'tutor1@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_my_tutor_returns_404_when_user_has_no_tutor()
    {
        $response = $this->getJson('/api/v1/me/tutor');

        $response->assertNotFound()
            ->assertJson(['error' => 'Tutor não encontrado']);
    }

    private function validCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = random_int(0, 9);
        }
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($i = 0; $i < $t; $i++) {
                $d += $n[$i] * (($t + 1) - $i);
            }
            $d = ((10 * $d) % 11) % 10;
            $n[$t] = $d;
        }
        return implode('', $n);
    }
}
