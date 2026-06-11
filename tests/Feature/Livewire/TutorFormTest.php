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
        $state = State::factory()->create();
        $city = \App\Models\City::factory()->create(['state_id' => $state->id]);

        Livewire::test('tutor-form')
            ->set('name', 'João Teste')
            ->set('cpf', '987.654.321-00')
            ->set('email', 'joao@teste.com')
            ->set('phone', '11999999999')
            ->set('state_id', (string) $state->id)
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
        $tutor = Tutor::factory()->create([
            'name' => 'Nome Antigo',
            'cpf' => '52998224725',
        ]);

        Livewire::test('tutor-form', ['id' => $tutor->id])
            ->set('name', 'Nome Novo')
            ->set('cpf', $tutor->cpf)
            ->set('email', $tutor->email)
            ->set('phone', $tutor->phone)
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Nome Novo']);
    }

    public function test_admin_user_can_create_tutor()
    {
        $admin = \App\Models\User::where('email', 'admin@vet.com')->first()
            ?? \App\Models\User::factory()->create(['email' => 'admin@vet.com', 'is_active' => true]);
        $this->actingAs($admin);

        Livewire::test('tutor-form')
            ->set('name', 'Admin Tutor')
            ->set('cpf', '111.222.333-44')
            ->set('email', 'admin-tutor@teste.com')
            ->set('phone', '11911111111')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['email' => 'admin-tutor@teste.com']);
    }

    public function test_super_user_can_create_tutor()
    {
        $super = \App\Models\User::where('email', 'super@vet.com')->first()
            ?? \App\Models\User::factory()->create(['email' => 'super@vet.com', 'is_active' => true]);
        $this->actingAs($super);

        Livewire::test('tutor-form')
            ->set('name', 'Super Tutor')
            ->set('cpf', '555.666.777-88')
            ->set('email', 'super-tutor@teste.com')
            ->set('phone', '11922222222')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['email' => 'super-tutor@teste.com']);
    }

    public function test_user_with_permission_can_create()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'PermTutor', 'guard_name' => 'web', 'slug' => 'perm-tutor']);
        $user = \App\Models\User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
        $user->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'tutors.create', 'guard_name' => 'web']));
        $user->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'tutors.view', 'guard_name' => 'web']));
        $this->actingAs($user);

        Livewire::test('tutor-form')
            ->set('name', 'Perm Tutor')
            ->set('cpf', '999.888.777-66')
            ->set('email', 'perm-tutor@teste.com')
            ->set('phone', '11933333333')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['email' => 'perm-tutor@teste.com']);
    }

    public function test_handles_duplicate_cpf_gracefully()
    {
        Tutor::factory()->create(['cpf' => '12345678909']);

        Livewire::test('tutor-form')
            ->set('name', 'CPF Duplicate')
            ->set('cpf', '12345678909')
            ->set('email', 'cpfdup@teste.com')
            ->set('phone', '11944444444')
            ->call('save')
            ->assertHasErrors('cpf');
    }

    public function test_can_update_tutor_via_event()
    {
        $tutor = Tutor::factory()->create([
            'name' => 'Event Tutor',
            'cpf' => '98765432100',
        ]);

        Livewire::test('tutor-form')
            ->dispatch('editTutor', id: $tutor->id)
            ->assertSet('tutorId', $tutor->id)
            ->assertSet('name', 'Event Tutor')
            ->assertSet('cpf', '98765432100')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Event Tutor']);
    }

    public function test_can_update_tutor_via_event_without_changing_cpf()
    {
        $tutor = Tutor::factory()->create([
            'name' => 'Same CPF Tutor',
            'cpf' => '11122233344',
        ]);

        Livewire::test('tutor-form')
            ->dispatch('editTutor', id: $tutor->id)
            ->assertSet('tutorId', $tutor->id)
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id]);
    }

    public function test_can_update_tutor_with_same_cpf_as_create_then_edit()
    {
        $tutor = Tutor::factory()->create([
            'name' => 'Create Then Edit',
            'cpf' => '55544433322',
        ]);

        Livewire::test('tutor-form')
            ->set('name', 'Temp')
            ->set('cpf', '99988877766')
            ->set('email', 'temp@teste.com')
            ->set('phone', '11955555555')
            ->dispatch('editTutor', id: $tutor->id)
            ->assertSet('tutorId', $tutor->id)
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Updated Name']);
    }

    public function test_can_update_tutor_filling_email_that_was_empty()
    {
        $tutor = Tutor::factory()->create([
            'name' => 'Fill Email',
            'cpf' => '66677788899',
            'email' => null,
        ]);

        Livewire::test('tutor-form')
            ->dispatch('editTutor', id: $tutor->id)
            ->assertSet('tutorId', $tutor->id)
            ->set('email', 'fill-email@teste.com')
            ->call('save')
            ->assertDispatched('tutor-saved');

        $this->assertDatabaseHas('tutors', [
            'id' => $tutor->id,
            'email' => 'fill-email@teste.com',
        ]);
    }

    public function test_prevents_duplicate_cpf_with_different_name()
    {
        Tutor::factory()->create([
            'name' => 'Original',
            'cpf' => '11122233344',
            'email' => 'original@teste.com',
        ]);

        Livewire::test('tutor-form')
            ->set('name', 'Different Name')
            ->set('cpf', '11122233344')
            ->set('email', 'different@teste.com')
            ->set('phone', '11977777777')
            ->call('save')
            ->assertHasErrors('cpf');
    }
}
