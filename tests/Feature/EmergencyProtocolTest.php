<?php

namespace Tests\Feature;

use App\Models\EmergencyProtocol;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmergencyProtocolTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_requires_authentication()
    {
        $this->get(route('emergency-protocols.index'))->assertRedirect(route('login'));
    }

    public function test_create_and_store()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ep-test-store', 'guard_name' => 'web']);
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'emergency-protocols.view', 'guard_name' => 'web']));
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'emergency-protocols.create', 'guard_name' => 'web']));
        $user->assignRole($role);

        $this->actingAs($user)->post(route('emergency-protocols.store'), [
            'title' => 'Atendimento Urgente',
            'severity' => 'critical',
            'procedure_steps' => "1. Avaliar ABCDE\n2. Estabilizar",
            'species' => 'Canina',
        ])->assertRedirect(route('emergency-protocols.index'));

        $this->assertDatabaseHas('emergency_protocols', ['title' => 'Atendimento Urgente']);
    }
}
