<?php

namespace Tests\Feature\Controllers;

use App\Models\ZoonoticDisease;
use Tests\ModuleTestCase;

class ZoonoticDiseaseControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral']);
        ZoonoticDisease::create(['name' => 'Leptospirose', 'category' => 'bacterial']);
        $response = $this->get(route('zoonotic-diseases.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_search()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral']);
        ZoonoticDisease::create(['name' => 'Leptospirose', 'category' => 'bacterial']);
        $response = $this->get(route('zoonotic-diseases.index', ['search' => 'Raiva']));
        $response->assertOk();
        $response->assertSee('Raiva');
    }

    public function test_index_filters_by_category()
    {
        ZoonoticDisease::create(['name' => 'Viral Disease', 'category' => 'viral']);
        ZoonoticDisease::create(['name' => 'Bacterial Disease', 'category' => 'bacterial']);
        $response = $this->get(route('zoonotic-diseases.index', ['category' => 'viral']));
        $response->assertOk();
        $response->assertSee('Viral Disease');
    }

    public function test_create()
    {
        $response = $this->get(route('zoonotic-diseases.create'));
        $response->assertOk();
    }

    public function test_store_creates_disease()
    {
        $response = $this->post(route('zoonotic-diseases.store'), [
            'name' => 'Raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'transmission' => 'Mordedura',
            'animal_symptoms' => 'Agressividade',
            'human_symptoms' => 'Hidrofobia',
            'incubation_period' => '1-3 meses',
            'prevention' => 'Vacinação',
            'treatment' => 'Suporte',
            'is_notifiable' => true,
            'species_affected' => ['canine', 'feline'],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('zoonotic-diseases.store'), []);
        $response->assertSessionHasErrors(['name', 'category']);
    }

    public function test_show()
    {
        $disease = ZoonoticDisease::create(['name' => 'Mostrar Doença', 'category' => 'viral']);
        $response = $this->get(route('zoonotic-diseases.show', $disease));
        $response->assertOk();
    }

    public function test_edit()
    {
        $disease = ZoonoticDisease::create(['name' => 'Editar Doença', 'category' => 'viral']);
        $response = $this->get(route('zoonotic-diseases.edit', $disease));
        $response->assertOk();
    }

    public function test_update_modifies_disease()
    {
        $disease = ZoonoticDisease::create(['name' => 'Old Name', 'category' => 'viral']);
        $response = $this->put(route('zoonotic-diseases.update', $disease), [
            'name' => 'Updated Name',
            'category' => 'bacterial',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('zoonotic_diseases', [
            'id' => $disease->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_destroy_deletes_disease()
    {
        $disease = ZoonoticDisease::create(['name' => 'Excluir Doença', 'category' => 'viral']);
        $response = $this->delete(route('zoonotic-diseases.destroy', $disease));
        $response->assertRedirect();
        $this->assertDatabaseMissing('zoonotic_diseases', ['id' => $disease->id]);
    }
}
