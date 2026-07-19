<?php

namespace Tests\Unit\Models;

use App\Models\PaymentGateway;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        PaymentGateway::create([
            'name' => 'PagarMe',
            'provider' => 'pagarme',
            'channel' => 'both',
            'is_active' => true,
            'is_sandbox' => true,
            'public_key' => 'pk_test_123',
            'secret_key' => 'sk_test_456',
            'webhook_secret' => 'whsec_789',
            'webhook_url' => 'https://api.vet.com/webhook',
            'config' => ['max_installments' => 12],
            'notes' => 'Gateway de pagamento',
            'branch_id' => null,
        ]);

        $this->assertDatabaseHas('payment_gateways', [
            'name' => 'PagarMe',
            'provider' => 'pagarme',
            'is_active' => true,
            'is_sandbox' => true,
        ]);
    }

    public function test_active_scope()
    {
        $ids = [];
        $ids[] = PaymentGateway::create(['name' => 'A', 'provider' => 'a', 'channel' => 'both', 'is_active' => true])->id;
        $ids[] = PaymentGateway::create(['name' => 'B', 'provider' => 'b', 'channel' => 'both', 'is_active' => false])->id;

        $this->assertCount(1, PaymentGateway::whereIn('id', $ids)->active()->get());
    }

    public function test_hidden_attributes()
    {
        $gateway = PaymentGateway::create([
            'name' => 'Teste',
            'provider' => 'teste',
            'channel' => 'both',
            'secret_key' => 'sk_secret',
            'webhook_secret' => 'wh_secret',
        ]);

        $json = $gateway->toArray();

        $this->assertArrayNotHasKey('secret_key', $json);
        $this->assertArrayNotHasKey('webhook_secret', $json);
    }
}
