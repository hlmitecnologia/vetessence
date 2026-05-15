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
        ZoonoticDisease::create([
            'name' => 'Raiva', 'slug' => 'raiva', 'category' => 'viral',
            'causative_agent' => 'Vírus rábico', 'transmission' => 'Mordedura',
            'is_notifiable' => true, 'is_active' => true,
        ]);
        $this->assertDatabaseHas('zoonotic_diseases', ['name' => 'Raiva', 'is_active' => true]);
    }

    public function test_active_scope()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral', 'is_active' => true]);
        ZoonoticDisease::create(['name' => 'Inativa', 'category' => 'viral', 'is_active' => false]);
        $this->assertCount(1, ZoonoticDisease::active()->get());
    }

    public function test_notifiable_scope()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral', 'is_notifiable' => true, 'is_active' => true]);
        ZoonoticDisease::create(['name' => 'Outra', 'category' => 'viral', 'is_notifiable' => false, 'is_active' => true]);
        $this->assertCount(1, ZoonoticDisease::notifiable()->get());
    }

    public function test_by_category_scope()
    {
        ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral', 'is_active' => true]);
        ZoonoticDisease::create(['name' => 'Sarna', 'category' => 'parasitic', 'is_active' => true]);
        $this->assertCount(1, ZoonoticDisease::byCategory('parasitic')->get());
    }

    public function test_category_label_accessor()
    {
        $d = ZoonoticDisease::create(['name' => 'Raiva', 'category' => 'viral', 'is_active' => true]);
        $this->assertEquals('Viral', $d->category_label);
    }

    public function test_slug_auto_generated()
    {
        $d = ZoonoticDisease::create(['name' => 'Leishmaniose', 'category' => 'bacterial', 'is_active' => true]);
        $this->assertEquals('leishmaniose', $d->slug);
    }
}
