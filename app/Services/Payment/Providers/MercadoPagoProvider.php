<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;

class MercadoPagoProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (maquininha)', $invoice);

        $transactionId = 'MP-PDV-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Cobrança iniciada na maquininha Mercado Pago Point. Aguardando pagamento...',
                'redirect_url' => null,
                'raw_response' => [
                    'id' => $transactionId,
                    'status' => 'pending',
                    'point_of_interaction' => ['type' => 'POINT'],
                    'sandbox' => true,
                ],
            ];
        }

        // TODO: Implementar SDK mercadopago/dx-php
        // $mp = new \MercadoPago\Payment();
        // $mp->transaction_amount = (float) $invoice->total;
        // $mp->point_of_interaction = ['type' => 'POINT'];
        // $mp->save();

        return $this->fallbackResponse($transactionId);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Mercado Pago)', $invoice);

        $transactionId = 'MP-CK-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Checkout Mercado Pago criado.',
                'redirect_url' => 'https://sandbox.mercadopago.com.br/checkout/v1/redirect?' . http_build_query([
                    'preference_id' => $transactionId,
                    'sandbox' => 'true',
                ]),
                'raw_response' => [
                    'id' => $transactionId,
                    'init_point' => 'https://sandbox.mercadopago.com.br/checkout/v1/redirect?...',
                    'sandbox' => true,
                ],
            ];
        }

        // TODO: Implementar SDK
        // $preference = new \MercadoPago\Preference();
        // $preference->items = [...];

        return $this->fallbackResponse($transactionId);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Mercado Pago', ['payload' => $payload]);

        $transactionId = $payload['data']['id'] ?? $payload['resource'] ?? null;
        $status = $payload['action'] ?? $payload['type'] ?? null;

        if (!$transactionId) {
            Log::warning('MercadoPago: webhook sem transaction_id', $payload);
            return null;
        }

        $statusMap = [
            'payment.created' => 'pending',
            'payment.updated' => 'pending',
            'payment.approved' => 'paid',
            'payment.rejected' => 'failed',
            'payment.refunded' => 'refunded',
            'payment.cancelled' => 'cancelled',
        ];

        return [
            'transaction_id' => $transactionId,
            'status' => $statusMap[$status] ?? 'pending',
            'paid_at' => $payload['data']['date_approved'] ?? $payload['date_approved'] ?? null,
            'gateway_status' => $status,
            'raw_response' => $payload,
        ];
    }

    public static function supportedChannels(): array
    {
        return ['portal', 'pdv', 'both'];
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
            Log::info("[MercadoPago][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
