<?php

namespace Tests\Unit\Services\Insurance;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Setting;
use App\Services\Insurance\PetloveProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class PetloveProviderTest extends ModuleTestCase
{
    private PetloveProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('petlove_api_key', 'test-key');
        Setting::set('petlove_api_secret', 'test-secret');
        Setting::set('petlove_base_url', 'https://api.petlove.com.br/v1');
        $this->provider = app(PetloveProvider::class);
    }

    public function test_get_name()
    {
        $this->assertEquals('petlove', $this->provider->getName());
    }

    public function test_submit_claim_success()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims' => Http::response([
                'id' => 'ext-123',
                'status' => 'received',
            ], 200),
        ]);

        $convenioPet = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
        ]);

        $result = $this->provider->submitClaim($claim);

        $this->assertTrue($result->success);
        $this->assertEquals('ext-123', $result->externalId);
    }

    public function test_submit_claim_fails_on_http_error()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims' => Http::response(['error' => 'Invalid policy'], 422),
        ]);

        $convenioPet = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
        ]);

        $result = $this->provider->submitClaim($claim);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('422', $result->message);
    }

    public function test_submit_claim_fails_when_no_external_id()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims' => Http::response(['status' => 'ok'], 200),
        ]);

        $claim = ConvenioClaim::factory()->create();

        $result = $this->provider->submitClaim($claim);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('External ID', $result->message);
    }

    public function test_check_status_success()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims/ext-123' => Http::response([
                'id' => 'ext-123',
                'status' => 'approved',
            ], 200),
        ]);

        $result = $this->provider->checkStatus('ext-123');

        $this->assertTrue($result->success);
        $this->assertEquals('ext-123', $result->externalId);
    }

    public function test_check_status_fails_on_http_error()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims/ext-123' => Http::response(null, 404),
        ]);

        $result = $this->provider->checkStatus('ext-123');

        $this->assertFalse($result->success);
    }

    public function test_check_eligibility_returns_data_on_success()
    {
        Http::fake([
            'api.petlove.com.br/v1/plans/pol-123/eligibility' => Http::response([
                'status' => 'active',
                'coverage' => ['vaccines', 'consultations'],
            ], 200),
        ]);

        $result = $this->provider->checkEligibility('pol-123');

        $this->assertEquals('active', $result['status']);
    }

    public function test_check_eligibility_returns_error_on_failure()
    {
        Http::fake([
            'api.petlove.com.br/v1/plans/pol-123/eligibility' => Http::response(null, 500),
        ]);

        $result = $this->provider->checkEligibility('pol-123');

        $this->assertEquals('unknown', $result['status']);
    }

    public function test_request_pre_authorization_success()
    {
        Http::fake([
            'api.petlove.com.br/v1/authorizations' => Http::response([
                'authorization_number' => 'auth-456',
                'status' => 'pre-approved',
            ], 200),
        ]);

        $result = $this->provider->requestPreAuthorization('pol-123', [
            ['code' => 'SURG01', 'description' => 'Cirurgia geral'],
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('auth-456', $result->externalId);
    }

    public function test_request_pre_authorization_fails()
    {
        Http::fake([
            'api.petlove.com.br/v1/authorizations' => Http::response(null, 500),
        ]);

        $result = $this->provider->requestPreAuthorization('pol-123', []);

        $this->assertFalse($result->success);
    }

    public function test_submit_claim_handles_exception()
    {
        Http::fake([
            'api.petlove.com.br/v1/claims' => function () {
                throw new \Exception('Connection timeout');
            },
        ]);

        $claim = ConvenioClaim::factory()->create();

        $result = $this->provider->submitClaim($claim);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Connection timeout', $result->message);
    }
}
