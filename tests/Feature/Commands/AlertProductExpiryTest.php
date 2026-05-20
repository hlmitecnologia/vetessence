<?php

namespace Tests\Feature\Commands;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AlertProductExpiryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_alert_expired_products()
    {
        Product::factory()->create(['expiration_date' => now()->subDay(), 'stock' => 10]);

        $this->artisan('products:alert-expiry')
            ->assertSuccessful();
    }

    public function test_alert_near_expiry()
    {
        Product::factory()->create(['expiration_date' => now()->addDays(15), 'stock' => 10]);

        $this->artisan('products:alert-expiry', ['--days' => 30])
            ->assertSuccessful();
    }

    public function test_skips_far_expiry()
    {
        Product::factory()->create(['expiration_date' => now()->addYear(), 'stock' => 10]);

        $this->artisan('products:alert-expiry', ['--days' => 30])
            ->assertSuccessful();
    }
}
