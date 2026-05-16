<?php

namespace Tests\Feature;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InsuranceWebhookTest extends TestCase
{
    use DatabaseTransactions;

    public function test_webhook_updates_claim()
    {
        $cp = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $cp->id,
            'status' => 'filed',
            'external_id' => 'EXT-001',
        ]);

        $response = $this->postJson(route('insurance.webhook'), [
            'external_id' => 'EXT-001',
            'status' => 'approved',
            'amount_approved' => 450.00,
        ]);
        $response->assertOk();
        $this->assertEquals('approved', $claim->fresh()->status);
        $this->assertEquals(450.00, $claim->fresh()->amount_approved);
    }

    public function test_webhook_rejects_missing_external_id()
    {
        $response = $this->postJson(route('insurance.webhook'), [
            'status' => 'approved',
        ]);
        $response->assertStatus(422);
    }

    public function test_webhook_returns_404_for_unknown_claim()
    {
        $response = $this->postJson(route('insurance.webhook'), [
            'external_id' => 'NONEXISTENT',
            'status' => 'approved',
        ]);
        $response->assertStatus(404);
    }
}
