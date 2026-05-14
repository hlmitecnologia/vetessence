<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\Invoice;

class PaymentService
{
    public function charge(Invoice $invoice, array $paymentData = []): array
    {
        $gateway = PaymentGateway::active()->first();

        if (!$gateway) {
            return ['success' => false, 'message' => 'Nenhum gateway de pagamento ativo.'];
        }

        // Placeholder: implement real gateway integration here
        // e.g., Mercado Pago SDK, Stripe SDK, etc.

        $invoice->update([
            'gateway_id' => $gateway->id,
            'gateway_transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'gateway_status' => 'pending',
        ]);

        return [
            'success' => true,
            'gateway' => $gateway->provider,
            'transaction_id' => $invoice->gateway_transaction_id,
            'sandbox' => $gateway->is_sandbox,
        ];
    }

    public function processWebhook(string $provider, array $payload): array
    {
        // Placeholder: validate webhook and update invoice status
        return ['success' => true, 'message' => 'Webhook received'];
    }

    public function getActiveGateway(): ?PaymentGateway
    {
        return PaymentGateway::active()->first();
    }
}
