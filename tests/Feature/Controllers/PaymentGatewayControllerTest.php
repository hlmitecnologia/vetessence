<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\PaymentGateway;
use Tests\ModuleTestCase;

class PaymentGatewayControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        PaymentGateway::factory()->count(3)->create();
        $response = $this->get(route('payment-gateways.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        Branch::factory()->create();
        $response = $this->get(route('payment-gateways.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('payment-gateways.store'), [
            'name' => 'Mercado Pago',
            'provider' => 'mercadopago',
            'channel' => 'portal',
            'is_active' => true,
            'is_sandbox' => true,
            'public_key' => 'pk-test-123',
            'secret_key' => 'sk-test-456',
        ]);
        $response->assertRedirect(route('payment-gateways.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_gateways', [
            'name' => 'Mercado Pago',
            'provider' => 'mercadopago',
            'is_active' => true,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('payment-gateways.store'), []);
        $response->assertSessionHasErrors(['name', 'provider', 'channel']);
    }

    public function test_store_accepts_config_as_json_string()
    {
        $response = $this->post(route('payment-gateways.store'), [
            'name' => 'Test Stripe',
            'provider' => 'stripe',
            'channel' => 'portal',
            'config' => json_encode(['url' => 'https://api.stripe.com']),
            'is_active' => true,
        ]);
        $response->assertRedirect(route('payment-gateways.index'));
        $response->assertSessionHas('success');
    }

    public function test_show()
    {
        $gateway = PaymentGateway::factory()->create();
        $response = $this->get(route('payment-gateways.show', $gateway));
        $response->assertOk();
    }

    public function test_edit()
    {
        Branch::factory()->create();
        $gateway = PaymentGateway::factory()->create();
        $response = $this->get(route('payment-gateways.edit', $gateway));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $gateway = PaymentGateway::factory()->create(['is_active' => false]);

        $response = $this->put(route('payment-gateways.update', $gateway), [
            'name' => 'Gateway Atualizado',
            'provider' => 'pix',
            'channel' => 'portal',
            'public_key' => 'pk_test_123',
            'is_active' => true,
            'is_sandbox' => false,
        ]);
        $response->assertRedirect(route('payment-gateways.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_gateways', [
            'id' => $gateway->id,
            'name' => 'Gateway Atualizado',
            'provider' => 'pix',
        ]);
    }

    public function test_update_deactivates_other_active_gateways()
    {
        $active = PaymentGateway::factory()->create(['is_active' => true, 'channel' => 'portal']);
        $gateway = PaymentGateway::factory()->create(['is_active' => false, 'channel' => 'portal']);

        $response = $this->put(route('payment-gateways.update', $gateway), [
            'name' => $gateway->name,
            'provider' => $gateway->provider,
            'channel' => 'portal',
            'is_active' => true,
            'is_sandbox' => true,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('payment_gateways', [
            'id' => $active->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('payment_gateways', [
            'id' => $gateway->id,
            'is_active' => true,
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $gateway = PaymentGateway::factory()->create();
        $response = $this->delete(route('payment-gateways.destroy', $gateway));
        $response->assertRedirect(route('payment-gateways.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('payment_gateways', ['id' => $gateway->id]);
    }
}
