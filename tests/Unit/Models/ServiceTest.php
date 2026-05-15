<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $category = Category::create(['type' => 'service', 'name' => 'General']);
        Service::create([
            'category_id' => $category->id,
            'name' => 'Consultation',
            'description' => 'General consult',
            'price' => 150.00,
            'duration' => 30,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Consultation',
            'price' => 150.00,
            'is_active' => true,
        ]);
    }

    public function test_category_relationship()
    {
        $category = Category::create(['type' => 'service', 'name' => 'General']);
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Consultation',
            'price' => 150.00,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Category::class, $service->category);
    }

    public function test_is_active_cast()
    {
        $service = Service::create([
            'name' => 'Consultation',
            'price' => 150.00,
            'is_active' => true,
        ]);

        $this->assertIsBool($service->is_active);
    }
}
