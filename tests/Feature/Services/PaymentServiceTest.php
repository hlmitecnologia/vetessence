<?php

namespace Tests\Feature\Services;

use App\Models\PaymentGateway;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Services\PaymentService;
use Tests\ModuleTestCase;

class PaymentServiceTest extends ModuleTestCase
{
    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        PaymentGateway::withoutBranch()->update(['is_active' => false]);
        $this->service = app(PaymentService::class);
    }

    public function test_get_active_gateway_returns_null_when_none_active()
    {
        PaymentGateway::factory()->create(['channel' => 'portal', 'is_active' => false, 'is_sandbox' => true]);

        $result = $this->service->charge(Invoice::factory()->create([
            'tutor_id' => Tutor::factory()->create()->id,
            'total' => 100.00,
            'status' => 'pending',
        ]));

        $this->assertFalse($result['success']);
    }

    public function test_mercadopago_checkout_returns_redirect()
    {
        // Deactivate any pre-existing gateways to avoid data pollution
        PaymentGateway::withoutBranch()->update(['is_active' => false]);

        PaymentGateway::factory()->create([
            'provider' => 'mercadopago', 'channel' => 'portal', 'is_active' => true, 'is_sandbox' => true,
        ]);

        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 150.00,
            'status' => 'pending',
        ]);

        $result = $this->service->checkout($invoice);

        $this->assertTrue($result['success']);
        $this->assertEquals('mercadopago', $result['gateway_provider']);
        $this->assertStringContainsString('sandbox.mercadopago.com.br', $result['redirect_url']);
    }

    public function test_charge_fails_without_active_gateway()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 150.00,
            'status' => 'pending',
        ]);

        $result = $this->service->charge($invoice);
        $this->assertFalse($result['success']);
    }

    public function test_process_webhook()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $gateway = PaymentGateway::factory()->create([
            'provider' => 'mercadopago', 'is_active' => true, 'is_sandbox' => true,
        ]);

        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 100.00,
            'status' => 'pending',
            'gateway_id' => $gateway->id,
        ]);

        $result = $this->service->processWebhook($gateway, [
            'action' => 'payment.updated',
            'data' => ['id' => (string) $invoice->id],
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals($invoice->id, $result['invoice_id']);
        $this->assertEquals('paid', $result['new_status']);
    }
}
