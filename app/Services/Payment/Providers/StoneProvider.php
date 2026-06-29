<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;

class StoneProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (maquininha Stone)', $invoice);

        $transactionId = 'ST-PDV-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Cobrança iniciada na maquininha Stone. Aguardando pagamento...',
                'redirect_url' => null,
                'raw_response' => [
                    'TransactionIdentifier' => $transactionId,
                    'Status' => 'Pending',
                    'sandbox' => true,
                ],
            ];
        }

        // TODO: Implementar API Stone Hub
        // POST https://api.stone.co/api/v1/transaction
        // Authorization: Bearer {secret_key}

        return $this->fallbackResponse($transactionId);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Stone)', $invoice);

        $transactionId = 'ST-CK-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Link de pagamento Stone criado.',
                'redirect_url' => 'https://sandbox.stone.co/checkout/' . $transactionId,
                'raw_response' => [
                    'id' => $transactionId,
                    'checkout_url' => 'https://sandbox.stone.co/checkout/' . $transactionId,
                    'sandbox' => true,
                ],
            ];
        }

        return $this->fallbackResponse($transactionId);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Stone', ['payload' => $payload]);

        $transactionId = $payload['TransactionIdentifier'] ?? $payload['transaction_id'] ?? $payload['id'] ?? null;
        $status = $payload['Status'] ?? $payload['status'] ?? null;

        if (!$transactionId) {
            Log::warning('Stone: webhook sem transaction_id', $payload);
            return null;
        }

        $statusMap = [
            'Pending' => 'pending',
            'Paid' => 'paid',
            'Approved' => 'paid',
            'Captured' => 'paid',
            'Failed' => 'failed',
            'Denied' => 'failed',
            'Refunded' => 'refunded',
            'Cancelled' => 'cancelled',
        ];

        return [
            'transaction_id' => $transactionId,
            'status' => $statusMap[$status] ?? 'pending',
            'paid_at' => $payload['PaidDate'] ?? $payload['paid_at'] ?? null,
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
            Log::info("[Stone][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
