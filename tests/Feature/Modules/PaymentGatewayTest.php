<?php

namespace Tests\Feature\Modules;

use App\Models\PaymentGateway;
use Tests\ModuleTestCase;

class PaymentGatewayTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        PaymentGateway::withoutBranch()->where('is_active', true)->update(['is_active' => false]);
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
            'channel' => 'portal',
            'is_active' => true,
            'is_sandbox' => false,
            'public_key' => 'pub_key_123',
            'secret_key' => 'sec_key_456',
            'config' => ['terminal_id' => '87654321'],
        ]);
        $this->assertDatabaseHas('payment_gateways', ['name' => 'Mercado Pago Produção']);
    }

    public function test_only_one_active()
    {
        $old = PaymentGateway::factory()->create(['is_active' => true, 'channel' => 'portal', 'provider' => 'mercadopago']);
        $response = $this->post(route('payment-gateways.store'), [
            'name' => 'Gateway 2', 'provider' => 'mercadopago', 'channel' => 'portal',
            'is_active' => true, 'is_sandbox' => true, 'secret_key' => 'sk_test',
            'config' => ['terminal_id' => '12345678'],
        ]);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('payment_gateways', ['id' => $old->id, 'is_active' => true]);
    }

    public function test_service_returns_gateway()
    {
        PaymentGateway::factory()->create(['is_active' => true, 'channel' => 'portal']);
        $service = app(\App\Services\PaymentService::class);
        $this->assertNotNull($service->getActiveGatewayForChannel('portal'));
    }
}
