<?php

namespace Tests\Feature\Livewire;

use App\Models\BreedDefault;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class BreedDefaultFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('breed-default-form')
            ->set('species', 'canino')
            ->set('breed', 'Labrador')
            ->set('size', 'grande')
            ->call('save')
            ->assertDispatched('breed-default-saved');

        $this->assertDatabaseHas('breed_defaults', ['breed' => 'Labrador']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('breed-default-form')
            ->call('save')
            ->assertHasErrors(['species', 'breed']);
    }

    public function test_can_edit(): void
    {
        $breedDefault = BreedDefault::factory()->create(['breed' => 'Poodle']);

        Livewire::test('breed-default-form')
            ->dispatch('editBreedDefault', id: $breedDefault->id)
            ->assertSet('breed', 'Poodle')
            ->set('breed', 'Poodle Atualizado')
            ->call('save')
            ->assertDispatched('breed-default-saved');

        $this->assertDatabaseHas('breed_defaults', ['id' => $breedDefault->id, 'breed' => 'Poodle Atualizado']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('breed-default-form')
            ->set('species', 'felino')
            ->set('breed', 'Siamês')
            ->dispatch('resetForm')
            ->assertSet('species', '')
            ->assertSet('breed', '')
            ->assertSet('size', '')
            ->assertSet('is_active', true);
    }

    public function test_validates_numeric_fields(): void
    {
        Livewire::test('breed-default-form')
            ->set('species', 'canino')
            ->set('breed', 'Test')
            ->set('avg_weight_min', -1)
            ->call('save')
            ->assertHasErrors(['avg_weight_min']);
    }
}
