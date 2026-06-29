<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use App\Services\PixService;
use Illuminate\Support\Facades\Log;

class PixStaticProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Gerando QR Code PIX para PDV', $invoice);

        $qrcode = $invoice->generatePixCode();

        return [
            'success' => true,
            'transaction_id' => 'PIX-' . $invoice->invoice_number,
            'status' => 'pending',
            'message' => 'QR Code PIX gerado. Escaneie para pagar.',
            'redirect_url' => null,
            'raw_response' => [
                'payload' => $qrcode['payload'],
                'qrcode_base64' => $qrcode['qrcode_base64'] ?? null,
            ],
        ];
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Gerando PIX estático para checkout', $invoice);

        $qrcode = $invoice->generatePixCode();

        return [
            'success' => true,
            'transaction_id' => 'PIX-' . $invoice->invoice_number,
            'status' => 'pending',
            'message' => 'QR Code PIX gerado. O pagamento será confirmado manualmente.',
            'redirect_url' => null,
            'raw_response' => [
                'payload' => $qrcode['payload'],
                'qrcode_base64' => $qrcode['qrcode_base64'] ?? null,
            ],
        ];
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        // PIX estático não tem webhook. O pagamento é confirmado manualmente.
        Log::info('PixStaticProvider: webhook recebido mas PIX estático não suporta confirmação automática.', $payload);
        return null;
    }

    public static function supportedChannels(): array
    {
        return ['portal', 'pdv'];
    }

    protected function log(string $message, mixed $context = []): void
    {
        if ($this->gateway->is_sandbox) {
            Log::info("[PIX][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
