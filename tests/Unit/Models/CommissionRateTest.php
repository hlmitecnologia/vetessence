<?php

namespace Tests\Unit\Models;

use App\Models\CommissionRate;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommissionRateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();
        CommissionRate::create([
            'user_id' => $user->id,
            'commissionable_type' => Service::class,
            'commissionable_id' => $service->id,
            'rate_type' => 'percentage',
            'rate_value' => 15.00,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('commission_rates', [
            'user_id' => $user->id,
            'rate_value' => 15.00,
        ]);
    }

    public function test_user_relationship()
    {
        $rate = CommissionRate::factory()->create();
        $this->assertInstanceOf(User::class, $rate->user);
    }

    public function test_commissionable_morph()
    {
        $rate = CommissionRate::factory()->create();
        $this->assertNotNull($rate->commissionable);
    }
}
