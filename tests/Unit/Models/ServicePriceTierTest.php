<?php

namespace Tests\Unit\Models;

use App\Models\Service;
use App\Models\ServicePriceTier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServicePriceTierTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $service = Service::factory()->create();
        ServicePriceTier::create([
            'service_id' => $service->id,
            'species' => 'Canina',
            'size' => 'pequeno',
            'price' => 150.00,
        ]);
        $this->assertDatabaseHas('service_price_tiers', [
            'species' => 'Canina',
            'price' => 150.00,
        ]);
    }

    public function test_price_cast()
    {
        $service = Service::factory()->create();
        $tier = ServicePriceTier::create([
            'service_id' => $service->id,
            'species' => 'Canina',
            'price' => 99.90,
        ]);
        $this->assertEquals('99.90', $tier->price);
    }

    public function test_service_relationship()
    {
        $service = Service::factory()->create();
        $tier = ServicePriceTier::create([
            'service_id' => $service->id,
            'species' => 'Canina',
            'price' => 100.00,
        ]);
        $this->assertTrue($tier->service->is($service));
    }

    public function test_get_price_returns_correct_value()
    {
        $service = Service::factory()->create();
        ServicePriceTier::create([
            'service_id' => $service->id,
            'species' => 'Canina',
            'size' => 'pequeno',
            'price' => 120.00,
        ]);
        $this->assertEquals(120.00, ServicePriceTier::getPrice($service->id, 'Canina', 'pequeno'));
    }

    public function test_get_price_falls_back_to_null_size()
    {
        $service = Service::factory()->create();
        ServicePriceTier::create([
            'service_id' => $service->id,
            'species' => 'Canina',
            'size' => null,
            'price' => 100.00,
        ]);
        $this->assertEquals(100.00, ServicePriceTier::getPrice($service->id, 'Canina', 'grande'));
    }

    public function test_get_price_returns_null_when_not_found()
    {
        $this->assertNull(ServicePriceTier::getPrice(999, 'Canina'));
    }
}
