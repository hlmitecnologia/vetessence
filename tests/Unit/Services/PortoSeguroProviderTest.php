<?php

namespace Tests\Unit\Services;

use App\Models\ConvenioClaim;
use App\Services\Insurance\PortoSeguroProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortoSeguroProviderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_returns_provider_name()
    {
        $provider = new PortoSeguroProvider();
        $this->assertEquals('Porto Seguro', $provider->getName());
    }

    public function test_submit_returns_false_without_api()
    {
        $claim = ConvenioClaim::factory()->create(['status' => 'draft']);
        $provider = new PortoSeguroProvider();
        $result = $provider->submit($claim);
        $this->assertFalse($result);
    }

    public function test_check_status_returns_filed_without_api()
    {
        $provider = new PortoSeguroProvider();
        $result = $provider->checkStatus('nonexistent-id');
        $this->assertEquals('filed', $result);
    }
}
