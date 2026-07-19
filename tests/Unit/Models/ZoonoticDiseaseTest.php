<?php

namespace Tests\Unit\Models;

use App\Models\ZoonoticDisease;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ZoonoticDiseaseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $slug = fake()->unique()->slug();
        ZoonoticDisease::create([
            'name' => 'Doenca Teste', 'slug' => $slug, 'category' => 'viral',
            'causative_agent' => 'Virus teste', 'transmission' => 'Mordedura',
            'is_notifiable' => true, 'is_active' => true,
        ]);
        $this->assertDatabaseHas('zoonotic_diseases', ['name' => 'Doenca Teste', 'is_active' => true]);
    }

    public function test_active_scope()
    {
        $ids = [];
        $ids[] = ZoonoticDisease::create(['name' => 'Ativa ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_active' => true])->id;
        $ids[] = ZoonoticDisease::create(['name' => 'Inativa ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_active' => false])->id;
        $this->assertCount(1, ZoonoticDisease::whereIn('id', $ids)->active()->get());
    }

    public function test_notifiable_scope()
    {
        $ids = [];
        $ids[] = ZoonoticDisease::create(['name' => 'Notif ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_notifiable' => true, 'is_active' => true])->id;
        $ids[] = ZoonoticDisease::create(['name' => 'Nao Notif ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_notifiable' => false, 'is_active' => true])->id;
        $this->assertCount(1, ZoonoticDisease::whereIn('id', $ids)->notifiable()->get());
    }

    public function test_by_category_scope()
    {
        $ids = [];
        $ids[] = ZoonoticDisease::create(['name' => 'Viral ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_active' => true])->id;
        $ids[] = ZoonoticDisease::create(['name' => 'Parasit ' . fake()->unique()->lexify('???'), 'category' => 'parasitic', 'is_active' => true])->id;
        $this->assertCount(1, ZoonoticDisease::whereIn('id', $ids)->byCategory('parasitic')->get());
    }

    public function test_category_label_accessor()
    {
        $d = ZoonoticDisease::create(['name' => 'Teste Viral ' . fake()->unique()->lexify('???'), 'category' => 'viral', 'is_active' => true]);
        $this->assertEquals('Viral', $d->category_label);
    }

    public function test_slug_auto_generated()
    {
        $d = ZoonoticDisease::create(['name' => 'Leishmaniose ' . fake()->unique()->lexify('???'), 'category' => 'bacterial', 'is_active' => true]);
        $this->assertStringContainsString('leishmaniose', $d->slug);
    }
}
