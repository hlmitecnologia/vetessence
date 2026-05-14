<?php

namespace Tests\Feature\Modules;

use App\Models\ParasiteControl;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class ParasiteControlTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index_returns_view()
    {
        $response = $this->get(route('parasite-controls.index'));
        $response->assertOk();
    }

    public function test_store_creates_control()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $vet = $this->makeUser('veterinario');
        $response = $this->post(route('parasite-controls.store'), [
            'pet_id' => $pet->id,
            'product_name' => 'Bravecto',
            'type' => 'flea',
            'application_date' => now()->format('Y-m-d'),
            'next_due_date' => now()->addMonths(3)->format('Y-m-d'),
            'vet_id' => $vet->id,
        ]);
        $response->assertRedirect(route('parasite-controls.index'));
        $this->assertDatabaseHas('parasite_controls', ['product_name' => 'Bravecto', 'pet_id' => $pet->id]);
    }

    public function test_overdue_scope()
    {
        $overdue = ParasiteControl::factory()->create(['next_due_date' => now()->subDay()]);
        $ok = ParasiteControl::factory()->create(['next_due_date' => now()->addMonth()]);
        $this->assertTrue(ParasiteControl::overdue()->where('id', $overdue->id)->exists());
    }
}
