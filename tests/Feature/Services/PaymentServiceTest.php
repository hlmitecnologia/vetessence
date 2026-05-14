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
        $this->service = app(PaymentService::class);
    }

    public function test_get_active_gateway()
    {
        PaymentGateway::factory()->create(['is_active' => true]);
        $this->assertNotNull($this->service->getActiveGateway());
    }

    public function test_get_active_gateway_returns_null_when_none_active()
    {
        $this->assertNull($this->service->getActiveGateway());
    }

    public function test_charge_uses_active_gateway()
    {
        $gateway = PaymentGateway::factory()->create([
            'provider' => 'mercadopago', 'is_active' => true, 'is_sandbox' => true,
        ]);

        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 150.00,
            'status' => 'pending',
        ]);

        $result = $this->service->charge($invoice);

        $this->assertTrue($result['success']);
        $this->assertEquals('mercadopago', $result['gateway']);
        $this->assertTrue($result['sandbox']);
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
        $result = $this->service->processWebhook('mercadopago', ['action' => 'payment.created']);
        $this->assertTrue($result['success']);
    }
}
