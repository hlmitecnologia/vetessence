<?php

namespace Tests\Feature\Integrations;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class BranchIsolationTest extends ModuleTestCase
{
    protected Branch $branchA;
    protected Branch $branchB;
    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->branchA = Branch::factory()->create(['name' => 'Unidade A', 'is_main' => true]);
        $this->branchB = Branch::factory()->create(['name' => 'Unidade B', 'is_main' => false]);
        $this->userB = User::factory()->create(['branch_id' => $this->branchB->id]);

        $userRole = \App\Models\Role::where('slug', 'veterinario')->first();
        if ($userRole) {
            $this->userB->role_id = $userRole->id;
            $this->userB->save();
        }
        $this->userB->assignRole('super-admin');
    }

    public function test_tutor_and_pet_are_global_across_branches()
    {
        $tutor = Tutor::factory()->create(['created_at_branch_id' => $this->branchA->id]);
        $pet = Pet::factory()->create(['created_at_branch_id' => $this->branchA->id]);

        $this->actingAs($this->userB);

        $response = $this->get(route('pets.show', $pet));
        $response->assertOk();

        $responseTutor = $this->get(route('tutors.show', $tutor));
        $responseTutor->assertOk();
    }

    public function test_appointment_is_branch_scoped()
    {
        $pet = Pet::factory()->create();
        $vetA = User::factory()->create(['branch_id' => $this->branchA->id]);

        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vetA->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'scheduled',
            'branch_id' => $this->branchA->id,
        ]);

        $this->actingAs($this->userB);

        $response = $this->get(route('appointments.show', $appointment));
        $response->assertStatus(404);
    }
}
