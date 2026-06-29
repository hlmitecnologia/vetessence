<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;

class PagSeguroProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (maquininha PagSeguro)', $invoice);

        $transactionId = 'PS-PDV-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Cobrança iniciada na maquininha PagSeguro Pro. Aguardando pagamento...',
                'redirect_url' => null,
                'raw_response' => [
                    'code' => $transactionId,
                    'status' => 'pending',
                    'sandbox' => true,
                ],
            ];
        }

        // TODO: Implementar SDK pagseguro/pagseguro-php-sdk
        // $pagseguro = \PagSeguro\Services\Transactions\Create::create(...);

        return $this->fallbackResponse($transactionId);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (PagSeguro)', $invoice);

        $transactionId = 'PS-CK-' . strtoupper(uniqid());

        if ($this->gateway->is_sandbox) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => '[SANDBOX] Checkout PagSeguro criado.',
                'redirect_url' => 'https://sandbox.pagseguro.uol.com.br/checkout/v2/cart.html?' . http_build_query([
                    'code' => $transactionId,
                ]),
                'raw_response' => [
                    'code' => $transactionId,
                    'redirect_url' => 'https://sandbox.pagseguro.uol.com.br/checkout/...',
                    'sandbox' => true,
                ],
            ];
        }

        return $this->fallbackResponse($transactionId);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook PagSeguro', ['payload' => $payload]);

        $notificationCode = $payload['notificationCode'] ?? $payload['notification_code'] ?? null;
        $notificationType = $payload['notificationType'] ?? $payload['notification_type'] ?? null;

        if (!$notificationCode) {
            Log::warning('PagSeguro: webhook sem notificationCode', $payload);
            return null;
        }

        // PagSeguro envia notificationCode; é preciso consultar API para obter detalhes.
        // Simulação: mapeamento direto.
        $statusMap = [
            '1' => 'pending',      // Aguardando pagamento
            '2' => 'pending',      // Em análise
            '3' => 'paid',         // Paga
            '4' => 'paid',         // Disponível
            '5' => 'pending',      // Em disputa
            '6' => 'refunded',     // Devolvida
            '7' => 'cancelled',    // Cancelada
        ];

        $statusCode = $payload['status'] ?? '1';

        return [
            'transaction_id' => $notificationCode,
            'status' => $statusMap[$statusCode] ?? 'pending',
            'paid_at' => $payload['netAmount'] ?? $payload['date'] ?? null,
            'gateway_status' => "status_{$statusCode}",
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
            'raw_response' => ['code' => $transactionId, 'simulated' => true],
        ];
    }

    protected function log(string $message, mixed $context = []): void
    {
        if ($this->gateway->is_sandbox) {
            Log::info("[PagSeguro][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
