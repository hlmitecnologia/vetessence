<?php

namespace Tests\Feature\Livewire;

use App\Models\ZoonoticDisease;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ZoonoticDiseaseFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_disease_with_required_fields()
    {
        Livewire::test('zoonotic-disease-form')
            ->set('name', 'Raiva')
            ->set('category', 'viral')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('zoonotic_diseases', ['name' => 'Raiva', 'category' => 'viral']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('zoonotic-disease-form')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_validates_category_must_be_valid_enum()
    {
        Livewire::test('zoonotic-disease-form')
            ->set('name', 'Doença Teste')
            ->set('category', 'invalid')
            ->call('save')
            ->assertHasErrors(['category']);
    }

    public function test_can_edit_existing_disease()
    {
        $disease = ZoonoticDisease::create([
            'name' => 'Leishmaniose',
            'category' => 'parasitic',
        ]);

        Livewire::test('zoonotic-disease-form', ['id' => $disease->id])
            ->assertSet('name', 'Leishmaniose')
            ->set('name', 'Leishmaniose Visceral')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'id' => $disease->id,
            'name' => 'Leishmaniose Visceral',
        ]);
    }

    public function test_can_edit_disease_via_event()
    {
        $disease = ZoonoticDisease::create([
            'name' => 'Brucelose',
            'category' => 'bacterial',
        ]);

        Livewire::test('zoonotic-disease-form')
            ->dispatch('editZoonoticDisease', id: $disease->id)
            ->assertSet('zoonoticDiseaseId', $disease->id)
            ->assertSet('name', 'Brucelose')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');
    }

    public function test_can_select_species_affected()
    {
        Livewire::test('zoonotic-disease-form')
            ->set('name', 'Toxoplasmose')
            ->set('category', 'parasitic')
            ->set('species_affected', ['canine', 'feline'])
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $disease = ZoonoticDisease::where('name', 'Toxoplasmose')->first();
        $this->assertEquals(['canine', 'feline'], $disease->species_affected);
    }

    public function test_can_set_notifiable_flag()
    {
        Livewire::test('zoonotic-disease-form')
            ->set('name', 'Raiva Notificável')
            ->set('category', 'viral')
            ->set('is_notifiable', true)
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'Raiva Notificável',
            'is_notifiable' => true,
        ]);
    }

    public function test_validates_duplicate_name()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral']);

        Livewire::test('zoonotic-disease-form')
            ->set('name', 'Raiva')
            ->set('category', 'viral')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_reset_form_clears_properties()
    {
        $disease = ZoonoticDisease::create(['name' => 'Teste', 'category' => 'viral']);

        Livewire::test('zoonotic-disease-form')
            ->dispatch('editZoonoticDisease', id: $disease->id)
            ->assertSet('zoonoticDiseaseId', $disease->id)
            ->dispatch('resetForm')
            ->assertSet('zoonoticDiseaseId', null)
            ->assertSet('name', '')
            ->assertSet('category', 'viral')
            ->assertSet('is_notifiable', false);
    }
}
