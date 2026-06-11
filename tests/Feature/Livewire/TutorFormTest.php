<?php

namespace Tests\Feature\Livewire;

use App\Models\State;
use App\Models\Tutor;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class TutorFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_can_create_tutor_without_state_city()
    {
        Livewire::test('tutor-form')
            ->set('name', 'Maria Teste')
            ->set('cpf', '123.456.789-09')
            ->set('email', 'maria@teste.com')
            ->set('phone', '11988888888')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['email' => 'maria@teste.com']);
    }

    public function test_can_create_tutor_with_state_city()
    {
        $state = State::first();

        Livewire::test('tutor-form')
            ->set('name', 'João Teste')
            ->set('cpf', '987.654.321-00')
            ->set('email', 'joao@teste.com')
            ->set('phone', '11999999999')
            ->set('state_id', $state->id)
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['email' => 'joao@teste.com', 'state_id' => $state->id]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('tutor-form')
            ->call('save')
            ->assertHasErrors(['name', 'cpf', 'email', 'phone']);
    }

    public function test_validates_unique_cpf()
    {
        Tutor::factory()->create(['cpf' => '52998224725']);

        Livewire::test('tutor-form')
            ->set('name', 'Duplicado')
            ->set('cpf', '52998224725')
            ->set('email', 'dup@teste.com')
            ->set('phone', '11977777777')
            ->call('save')
            ->assertHasErrors(['cpf']);
    }

    public function test_validates_unique_email()
    {
        Tutor::factory()->create(['email' => 'existente@teste.com']);

        Livewire::test('tutor-form')
            ->set('name', 'Duplicado')
            ->set('cpf', '111.222.333-44')
            ->set('email', 'existente@teste.com')
            ->set('phone', '11966666666')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_can_update_tutor()
    {
        $tutor = Tutor::factory()->create(['name' => 'Nome Antigo']);

        Livewire::test('tutor-form', ['id' => $tutor->id])
            ->set('name', 'Nome Novo')
            ->set('cpf', $tutor->cpf)
            ->set('email', $tutor->email)
            ->set('phone', $tutor->phone)
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Nome Novo']);
    }

    public function test_redirects_when_edit_view_accessed()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->get(route('tutors.edit', $tutor));
        $response->assertRedirect(route('tutors.index'));
    }

    public function test_redirects_when_create_view_accessed()
    {
        $response = $this->get(route('tutors.create'));
        $response->assertRedirect(route('tutors.index'));
    }
}
