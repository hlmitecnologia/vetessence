<?php

namespace Tests\Feature\Livewire;

use App\Models\Hospitalization;
use App\Models\ExecutionMap;
use App\Models\ExecutionTask;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ExecutionMapIndexTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_render_index()
    {
        Livewire::test('execution-map-index')
            ->assertOk();
    }

    public function test_lists_hospitalizations()
    {
        $hospitalization = Hospitalization::factory()->create();

        Livewire::test('execution-map-index')
            ->assertSee($hospitalization->pet->name);
    }

    public function test_filters_by_status()
    {
        $admitted = Hospitalization::factory()->create(['status' => 'admitted']);
        $discharged = Hospitalization::factory()->create(['status' => 'discharged']);

        Livewire::test('execution-map-index')
            ->set('statusFilter', 'admitted')
            ->assertSee($admitted->pet->name)
            ->assertDontSee($discharged->pet->name);
    }

    public function test_searches_by_pet_name()
    {
        $pet1 = Pet::factory()->create(['name' => 'Rex']);
        $pet2 = Pet::factory()->create(['name' => 'Mimi']);
        Hospitalization::factory()->create(['pet_id' => $pet1->id]);
        Hospitalization::factory()->create(['pet_id' => $pet2->id]);

        Livewire::test('execution-map-index')
            ->set('search', 'Rex')
            ->assertSee('Rex')
            ->assertDontSee('Mimi');
    }

    public function test_shows_pending_count()
    {
        $hospitalization = Hospitalization::factory()->create();
        $map = ExecutionMap::factory()->create([
            'hospitalization_id' => $hospitalization->id,
            'date' => now()->toDateString(),
        ]);
        ExecutionTask::factory()->create(['execution_map_id' => $map->id, 'status' => 'pending']);
        ExecutionTask::factory()->completed()->create(['execution_map_id' => $map->id]);
        ExecutionTask::factory()->create(['execution_map_id' => $map->id, 'status' => 'pending']);

        Livewire::test('execution-map-index')
            ->assertSee($hospitalization->pet->name);
    }

    public function test_prioritizes_admitted()
    {
        Hospitalization::factory()->create(['status' => 'discharged', 'admission_date' => now()->subDays(5)]);
        Hospitalization::factory()->create(['status' => 'admitted', 'admission_date' => now()->subDays(2)]);

        $component = Livewire::test('execution-map-index');
        $hospitalizations = $component->hospitalizations;
        $this->assertEquals('admitted', $hospitalizations->first()->status);
    }
}
