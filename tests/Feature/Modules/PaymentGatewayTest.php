<?php

namespace Tests\Feature\Modules;

use App\Models\PaymentGateway;
use Tests\ModuleTestCase;

class PaymentGatewayTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('payment-gateways.index'));
        $response->assertOk();
    }

    public function test_store_creates_gateway()
    {
        $response = $this->post(route('payment-gateways.store'), [
            'name' => 'Mercado Pago Produção',
            'provider' => 'mercadopago',
            'is_active' => true,
            'is_sandbox' => false,
            'public_key' => 'pub_key_123',
            'secret_key' => 'sec_key_456',
        ]);
        $response->assertRedirect(route('payment-gateways.index'));
        $this->assertDatabaseHas('payment_gateways', ['name' => 'Mercado Pago Produção']);
    }

    public function test_only_one_active()
    {
        PaymentGateway::factory()->create(['is_active' => true]);
        $this->post(route('payment-gateways.store'), [
            'name' => 'Gateway 2', 'provider' => 'pix',
            'is_active' => true, 'is_sandbox' => true,
        ]);
        $this->assertEquals(1, PaymentGateway::active()->count());
    }

    public function test_service_returns_gateway()
    {
        PaymentGateway::factory()->create(['is_active' => true]);
        $service = app(\App\Services\PaymentService::class);
        $this->assertNotNull($service->getActiveGateway());
    }
}
