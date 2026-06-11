<?php

namespace Tests\Feature\Controllers;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InsuranceWebhookControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_webhook_updates_claim_status(): void
    {
        $cp = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $cp->id,
            'status' => 'submitted',
            'external_id' => 'EXT-001',
        ]);

        $response = $this->postJson(route('insurance.webhook', ['provider' => 'porto-seguro']), [
            'external_id' => 'EXT-001',
            'status' => 'approved',
            'amount_approved' => 450.00,
        ]);

        $response->assertOk();
        $this->assertEquals('approved', $claim->fresh()->status);
        $this->assertEquals(450.00, $claim->fresh()->amount_approved);
    }

    public function test_webhook_requires_valid_payload(): void
    {
        $response = $this->postJson(route('insurance.webhook', ['provider' => 'porto-seguro']), [
            'status' => 'approved',
        ]);

        $response->assertStatus(422);
    }

    public function test_webhook_returns_404_for_unknown_claim(): void
    {
        $response = $this->postJson(route('insurance.webhook', ['provider' => 'porto-seguro']), [
            'external_id' => 'NONEXISTENT',
            'status' => 'approved',
        ]);

        $response->assertStatus(404);
    }
}
