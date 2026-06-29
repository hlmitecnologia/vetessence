<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;

class StripeProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Tentativa de cobrança PDV rejeitada (Stripe não suporta maquininha no Brasil)', $invoice);

        return [
            'success' => false,
            'transaction_id' => null,
            'status' => 'failed',
            'message' => 'Stripe não suporta PDV com maquininha de cartão no Brasil. Configure um gateway PDV (Mercado Pago, PagSeguro ou Stone).',
            'redirect_url' => null,
            'raw_response' => [],
        ];
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Stripe)', $invoice);

        $transactionId = 'STR-CK-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Sessão de checkout Stripe criada.',
                'redirect_url' => 'https://checkout.stripe.com/c/pay/' . $transactionId,
                'raw_response' => [
                    'id' => $transactionId,
                    'url' => 'https://checkout.stripe.com/c/pay/' . $transactionId,
                    'sandbox' => true,
                ],
            ];
        }

        // TODO: Implementar SDK stripe/stripe-php
        // $session = \Stripe\Checkout\Session::create([...]);

        return $this->fallbackResponse($transactionId);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Stripe', ['payload' => $payload]);

        $type = $payload['type'] ?? null;
        $data = $payload['data']['object'] ?? [];

        if (!in_array($type, ['checkout.session.completed', 'payment_intent.succeeded', 'charge.succeeded'])) {
            Log::info('Stripe: webhook ignorado (tipo não mapeado)', ['type' => $type]);
            return null;
        }

        $transactionId = $data['id'] ?? $data['payment_intent'] ?? null;

        if (!$transactionId) {
            Log::warning('Stripe: webhook sem transaction_id', $payload);
            return null;
        }

        return [
            'transaction_id' => $transactionId,
            'status' => 'paid',
            'paid_at' => $data['created'] ?? $data['charges']['data'][0]['created'] ?? null,
            'gateway_status' => $type,
            'raw_response' => $payload,
        ];
    }

    public static function supportedChannels(): array
    {
        return ['portal'];
    }

    protected function fallbackResponse(string $transactionId): array
    {
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'pending',
            'message' => 'Cobrança gerada. Integração via SDK pendente — transação simulada.',
            'redirect_url' => null,
            'raw_response' => ['id' => $transactionId, 'simulated' => true],
        ];
    }

    protected function log(string $message, mixed $context = []): void
    {
        if ($this->gateway->is_sandbox) {
            Log::info("[Stripe][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
