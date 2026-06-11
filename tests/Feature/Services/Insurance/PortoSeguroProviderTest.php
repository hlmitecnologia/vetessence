<?php

namespace Tests\Feature\Services\Insurance;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Setting;
use App\Services\Insurance\PortoSeguroProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class PortoSeguroProviderTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected PortoSeguroProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('porto_seguro_api_key', 'test-key');
        Setting::set('porto_seguro_api_secret', 'test-secret');
        Setting::set('porto_seguro_base_url', 'https://api.portoseguro.test/v1');

        $this->provider = new PortoSeguroProvider;
    }

    public function test_submit_claim_success(): void
    {
        Http::fake([
            'api.portoseguro.test/v1/claims' => Http::response([
                'id' => 'PS-12345',
                'status' => 'received',
            ], 200),
        ]);

        $convenioPet = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
            'status' => 'pending',
        ]);

        $result = $this->provider->submitClaim($claim);

        $this->assertTrue($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
        $this->assertEquals('PS-12345', $result->externalId);
    }

    public function test_submit_claim_failure(): void
    {
        Http::fake([
            'api.portoseguro.test/v1/claims' => Http::response([
                'error' => 'Invalid policy number',
            ], 400),
        ]);

        $convenioPet = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
            'status' => 'pending',
        ]);

        $result = $this->provider->submitClaim($claim);

        $this->assertFalse($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
        $this->assertStringContainsString('Invalid policy number', $result->message ?? '');
    }

    public function test_check_status_success(): void
    {
        Http::fake([
            'api.portoseguro.test/v1/claims/PS-12345' => Http::response([
                'id' => 'PS-12345',
                'status' => 'approved',
                'amount_approved' => 450.00,
            ], 200),
        ]);

        $result = $this->provider->checkStatus('PS-12345');

        $this->assertTrue($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
        $this->assertEquals('PS-12345', $result->externalId);
        $this->assertEquals('approved', $result->rawResponse['status']);
    }
}
