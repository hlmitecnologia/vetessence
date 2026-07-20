<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoProvider implements PaymentGatewayProvider
{
    protected bool $useApi = false;

    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        return $this->errorResponse('Mercado Pago não suporta cobrança PDV/maquininha. Use o canal Portal para checkout online.');
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Mercado Pago)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCheckout($invoice);
        }

        return $this->simulatedCheckout($invoice);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Mercado Pago', ['payload' => $payload]);

        $transactionId = $payload['data']['id'] ?? $payload['resource'] ?? null;

        if (!$transactionId) {
            Log::warning('MercadoPago: webhook sem transaction_id', $payload);
            return null;
        }

        if ($this->hasCredentials()) {
            return $this->apiVerifyWebhook($transactionId, $payload);
        }

        return $this->simulatedVerifyWebhook($transactionId, $payload);
    }

    public static function supportedChannels(): array
    {
        return ['portal'];
    }

    protected function hasCredentials(): bool
    {
        return !empty($this->gateway->secret_key);
    }

    protected function setupMercadoPago(): bool
    {
        if (!$this->hasCredentials()) {
            return false;
        }

        MercadoPagoConfig::setAccessToken($this->gateway->secret_key);
        MercadoPagoConfig::setRuntimeEnviroment(
            $this->gateway->is_sandbox
                ? MercadoPagoConfig::LOCAL
                : MercadoPagoConfig::SERVER
        );
        $this->useApi = true;
        return true;
    }

    protected function simulatedCheckout(Invoice $invoice): array
    {
        $transactionId = 'MP-CK-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Checkout Mercado Pago criado.',
            'redirect_url' => 'https://sandbox.mercadopago.com.br/checkout/v1/redirect?' . http_build_query([
                'preference_id' => $transactionId,
                'sandbox' => 'true',
            ]),
            'raw_response' => [
                'id' => $transactionId,
                'init_point' => 'https://sandbox.mercadopago.com.br/checkout/v1/redirect?...',
                'simulated' => true,
            ],
        ];
    }

    protected function apiCheckout(Invoice $invoice): array
    {
        if (!$this->setupMercadoPago()) {
            return $this->errorResponse('Mercado Pago não configurado: access_token ausente.');
        }

        try {
            $client = new PreferenceClient();

            $items = [];
            foreach ($invoice->items as $item) {
                $items[] = [
                    'title' => strip_tags($item->description),
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'currency_id' => 'BRL',
                ];
            }

            if (empty($items)) {
                $items[] = [
                    'title' => 'Fatura ' . $invoice->invoice_number,
                    'quantity' => 1,
                    'unit_price' => (float) $invoice->total,
                    'currency_id' => 'BRL',
                ];
            }

            $request = [
                'items' => $items,
                'external_reference' => (string) $invoice->id,
                'notification_url' => $this->gateway->webhook_url,
                'statement_descriptor' => 'VETESSENCE',
                'auto_return' => 'approved',
                'back_urls' => [
                    'success' => route('portal.invoices.show', $invoice->id),
                    'failure' => route('portal.invoices.show', $invoice->id),
                    'pending' => route('portal.invoices.show', $invoice->id),
                ],
                'payment_methods' => [
                    'installments' => 12,
                ],
            ];

            $preference = $client->create($request);

            return [
                'success' => true,
                'transaction_id' => $preference->id,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Checkout Mercado Pago criado.',
                'redirect_url' => $preference->init_point,
                'raw_response' => json_decode(json_encode($preference), true),
            ];
        } catch (MPApiException $e) {
            $content = $e->getApiResponse()->getContent();
            Log::error('[MercadoPago] Erro na API ao criar checkout', ['response' => $content]);
            return $this->errorResponse('Erro Mercado Pago: ' . ($content['message'] ?? 'erro desconhecido'));
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro inesperado ao criar checkout', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro inesperado: ' . $e->getMessage());
        }
    }

    protected function simulatedVerifyWebhook(string $transactionId, array $payload): ?array
    {
        $action = $payload['action'] ?? $payload['type'] ?? '';

        if (str_starts_with($action, 'payment.')) {
            return [
                'transaction_id' => $transactionId,
                'reference' => ctype_digit($transactionId) ? $transactionId : ($payload['external_reference'] ?? $payload['reference'] ?? null),
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_status' => 'approved',
                'raw_response' => $payload,
            ];
        }

        return [
            'transaction_id' => $transactionId,
            'reference' => null,
            'status' => 'pending',
            'paid_at' => null,
            'gateway_status' => $action,
            'raw_response' => $payload,
        ];
    }

    protected function apiVerifyWebhook(string $transactionId, array $payload): ?array
    {
        if (!$this->setupMercadoPago()) {
            Log::warning('[MercadoPago] Webhook ignorado — access_token ausente.');
            return null;
        }

        try {
            $client = new PaymentClient();
            $payment = $client->get((int) $transactionId);

            $status = $payment->status ?? 'pending';

            return [
                'transaction_id' => (string) $payment->id,
                'reference' => $payment->external_reference,
                'status' => $this->mapStatus($status),
                'paid_at' => $payment->date_approved ? new \DateTime($payment->date_approved) : null,
                'gateway_status' => $status,
                'raw_response' => json_decode(json_encode($payment), true),
            ];
        } catch (MPApiException $e) {
            Log::warning('[MercadoPago] Erro ao consultar pagamento na API', [
                'transaction_id' => $transactionId,
                'status_code' => $e->getApiResponse()->getStatusCode(),
                'content' => $e->getApiResponse()->getContent(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro inesperado ao consultar pagamento', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function mapStatus(?string $mpStatus): string
    {
        return match ($mpStatus) {
            'approved' => 'paid',
            'rejected' => 'failed',
            'refunded' => 'refunded',
            'cancelled' => 'cancelled',
            'in_process' => 'pending',
            'pending' => 'pending',
            'authorized' => 'pending',
            default => 'pending',
        };
    }

    protected function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'transaction_id' => null,
            'status' => 'failed',
            'redirect_url' => null,
            'raw_response' => [],
        ];
    }

    protected function log(string $message, mixed $context = []): void
    {
        if ($this->gateway->is_sandbox) {
            Log::info("[MercadoPago][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
