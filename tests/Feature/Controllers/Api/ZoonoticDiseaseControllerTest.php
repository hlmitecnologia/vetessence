<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\ZoonoticDisease;
use Tests\ModuleTestCase;

class ZoonoticDiseaseControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_returns_active_diseases()
    {
        ZoonoticDisease::create([
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'is_active' => true,
        ]);
        ZoonoticDisease::create([
            'name' => 'Inativa',
            'slug' => 'inativa',
            'category' => 'bacterial',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Raiva']);
    }

    public function test_index_filters_by_search()
    {
        ZoonoticDisease::create([
            'name' => 'Leptospirose',
            'slug' => 'leptospirose',
            'category' => 'bacterial',
            'causative_agent' => 'Leptospira',
            'is_active' => true,
        ]);
        ZoonoticDisease::create([
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases?search=Lepto');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Leptospirose']);
    }

    public function test_index_filters_by_category()
    {
        ZoonoticDisease::create([
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'is_active' => true,
        ]);
        ZoonoticDisease::create([
            'name' => 'Leptospirose',
            'slug' => 'leptospirose',
            'category' => 'bacterial',
            'causative_agent' => 'Leptospira',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases?category=viral');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Raiva']);
    }

    public function test_show_returns_disease()
    {
        $disease = ZoonoticDisease::create([
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases/' . $disease->id);

        $response->assertOk()
            ->assertJson(['name' => 'Raiva']);
    }

    public function test_show_returns_404_for_inactive_disease()
    {
        $disease = ZoonoticDisease::create([
            'name' => 'Inativa',
            'slug' => 'inativa',
            'category' => 'bacterial',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases/' . $disease->id);

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_nonexistent_disease()
    {
        $response = $this->getJson('/api/v1/zoonotic-diseases/99999');

        $response->assertNotFound();
    }

    public function test_notifiable_returns_notifiable_diseases()
    {
        ZoonoticDisease::create([
            'name' => 'Raiva',
            'slug' => 'raiva',
            'category' => 'viral',
            'causative_agent' => 'Lyssavirus',
            'is_notifiable' => true,
            'is_active' => true,
        ]);
        ZoonoticDisease::create([
            'name' => 'Leptospirose',
            'slug' => 'leptospirose',
            'category' => 'bacterial',
            'is_notifiable' => false,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/zoonotic-diseases/notifiable/list');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Raiva']);
    }

    public function test_categories_returns_category_list()
    {
        $response = $this->getJson('/api/v1/zoonotic-diseases/categories/list');

        $response->assertOk()
            ->assertJson([
                'viral' => 'Viral',
                'bacterial' => 'Bacteriana',
                'parasitic' => 'Parasitária',
                'fungal' => 'Fúngica',
                'prion' => 'Prion',
            ]);
    }
}
