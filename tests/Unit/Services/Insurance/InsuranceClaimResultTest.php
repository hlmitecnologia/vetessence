<?php

namespace Tests\Unit\Services\Insurance;

use App\Services\Insurance\InsuranceClaimResult;
use Tests\TestCase;

class InsuranceClaimResultTest extends TestCase
{
    public function test_success_factory(): void
    {
        $result = InsuranceClaimResult::success('porto-seguro', 'EXT-001', ['id' => 'EXT-001']);

        $this->assertTrue($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
        $this->assertEquals('EXT-001', $result->externalId);
        $this->assertNull($result->message);
        $this->assertEquals(['id' => 'EXT-001'], $result->rawResponse);
    }

    public function test_failed_factory(): void
    {
        $result = InsuranceClaimResult::failed('porto-seguro', 'API error', ['error' => 'Unauthorized']);

        $this->assertFalse($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
        $this->assertNull($result->externalId);
        $this->assertEquals('API error', $result->message);
        $this->assertEquals(['error' => 'Unauthorized'], $result->rawResponse);
    }

    public function test_property_readonly(): void
    {
        $result = InsuranceClaimResult::success('porto-seguro', 'EXT-001');

        $this->assertTrue($result->success);
        $this->assertEquals('porto-seguro', $result->provider);
    }
}
