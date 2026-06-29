<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class StoneProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (Stone Hub)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCharge($invoice);
        }

        return $this->simulatedCharge($invoice);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Stone Link)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCheckout($invoice);
        }

        return $this->simulatedCheckout($invoice);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Stone', ['payload' => $payload]);

        $transactionId = $payload['TransactionIdentifier'] ?? $payload['transaction_id'] ?? $payload['id'] ?? null;

        if (!$transactionId) {
            Log::warning('Stone: webhook sem transaction_id', $payload);
            return null;
        }

        if ($this->hasCredentials()) {
            return $this->apiVerifyWebhook($transactionId, $payload);
        }

        return $this->simulatedVerifyWebhook($transactionId, $payload);
    }

    public static function supportedChannels(): array
    {
        return ['portal', 'pdv', 'both'];
    }

    protected function hasCredentials(): bool
    {
        return !empty($this->gateway->public_key) && !empty($this->gateway->secret_key);
    }

    protected function simulatedCharge(Invoice $invoice): array
    {
        $transactionId = 'ST-PDV-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Cobrança iniciada na maquininha Stone Hub. Aguardando pagamento...',
            'redirect_url' => null,
            'raw_response' => [
                'TransactionIdentifier' => $transactionId,
                'Status' => 'Pending',
                'simulated' => true,
            ],
        ];
    }

    protected function apiCharge(Invoice $invoice): array
    {
        try {
            $client = $this->makeClient();
            $response = $client->post('/v2/payments', [
                'json' => $this->buildChargePayload($invoice),
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                throw new \RuntimeException('Resposta inválida da API Stone.');
            }

            return [
                'success' => true,
                'transaction_id' => $body['id'] ?? $body['TransactionIdentifier'] ?? null,
                'reference' => (string) $invoice->id,
                'status' => $this->mapStatus($body['Status'] ?? $body['status'] ?? 'Pending'),
                'message' => 'Cobrança Stone Hub criada.',
                'redirect_url' => null,
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[Stone] Erro na API ao criar cobrança', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return $this->errorResponse('Erro Stone: ' . $e->getMessage());
        }
    }

    protected function simulatedCheckout(Invoice $invoice): array
    {
        $transactionId = 'ST-CK-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Checkout Stone Link criado.',
            'redirect_url' => 'https://checkout.stone.com.br/' . $transactionId,
            'raw_response' => [
                'id' => $transactionId,
                'checkout_url' => 'https://checkout.stone.com.br/' . $transactionId,
                'simulated' => true,
            ],
        ];
    }

    protected function apiCheckout(Invoice $invoice): array
    {
        try {
            $client = $this->makeClient();

            $items = $invoice->relationLoaded('items')
                ? $invoice->items->map(fn ($item) => [
                    'description' => strip_tags($item->description ?? 'Item'),
                    'quantity' => max(1, (int) ($item->quantity ?? 1)),
                    'amount' => (int) round(($item->unit_price ?? 0) * 100),
                ])->toArray()
                : [];

            $response = $client->post('/v2/checkouts', [
                'json' => [
                    'amount' => (int) round($invoice->total * 100),
                    'currency' => 'BRL',
                    'reference' => (string) $invoice->id,
                    'description' => 'Fatura ' . $invoice->invoice_number,
                    'items' => $items,
                    'redirect_url' => route('portal.invoices.show', $invoice->id),
                    'notification_url' => $this->gateway->webhook_url,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                throw new \RuntimeException('Resposta inválida da API Stone.');
            }

            return [
                'success' => true,
                'transaction_id' => $body['id'] ?? null,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Checkout Stone Link criado.',
                'redirect_url' => $body['checkout_url'] ?? $body['redirect_url'] ?? null,
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[Stone] Erro na API ao criar checkout', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return $this->errorResponse('Erro Stone: ' . $e->getMessage());
        }
    }

    protected function simulatedVerifyWebhook(string $transactionId, array $payload): ?array
    {
        $status = $payload['Status'] ?? $payload['status'] ?? null;

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
            'reference' => ctype_digit($transactionId) ? $transactionId : ($payload['reference'] ?? $payload['data']['id'] ?? null),
            'status' => $statusMap[$status] ?? 'paid',
            'paid_at' => in_array($status, ['Paid', 'Approved', 'Captured']) ? now() : null,
            'gateway_status' => $status ?? 'paid',
            'raw_response' => $payload,
        ];
    }

    protected function apiVerifyWebhook(string $transactionId, array $payload): ?array
    {
        try {
            $client = $this->makeClient();
            $response = $client->get("/v2/payments/{$transactionId}");

            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                throw new \RuntimeException('Resposta inválida da API Stone.');
            }

            $status = $body['Status'] ?? $body['status'] ?? 'Pending';

            return [
                'transaction_id' => $transactionId,
                'reference' => $body['reference'] ?? $body['Reference'] ?? null,
                'status' => $this->mapStatus($status),
                'paid_at' => in_array($status, ['Paid', 'Approved', 'Captured']) ? new \DateTime() : null,
                'gateway_status' => $status,
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::warning('[Stone] Erro ao consultar transação na API', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function makeClient(): Client
    {
        $baseUri = $this->gateway->is_sandbox
            ? 'https://sandbox-api.stone.co'
            : 'https://api.stone.co';

        $headers = ['Accept' => 'application/json'];

        if ($this->hasCredentials()) {
            $token = $this->authenticate($baseUri);
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
                $headers['Content-Type'] = 'application/json';
            }
        }

        return new Client([
            'base_uri' => $baseUri,
            'timeout' => 15,
            'headers' => $headers,
        ]);
    }

    protected function authenticate(string $baseUri): ?string
    {
        try {
            $httpClient = new Client([
                'base_uri' => $baseUri,
                'timeout' => 15,
                'headers' => ['Accept' => 'application/json'],
            ]);

            $response = $httpClient->post('/v2/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->gateway->public_key,
                    'client_secret' => $this->gateway->secret_key,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body) || empty($body['access_token'])) {
                Log::error('[Stone] OAuth retornou sem access_token', ['body' => $body]);
                return null;
            }

            return $body['access_token'];
        } catch (\Exception $e) {
            Log::error('[Stone] Erro na autenticação OAuth', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildChargePayload(Invoice $invoice): array
    {
        $payload = [
            'amount' => (int) round($invoice->total * 100),
            'currency' => 'BRL',
            'reference' => (string) $invoice->id,
            'description' => 'Fatura ' . $invoice->invoice_number,
        ];

        if ($this->gateway->is_sandbox) {
            $payload['payment_method'] = 'credit_card';
            $payload['capture'] = true;
        }

        return $payload;
    }

    protected function mapStatus(?string $stoneStatus): string
    {
        return match ($stoneStatus) {
            'Pending' => 'pending',
            'Paid', 'Approved', 'Captured' => 'paid',
            'Failed', 'Denied' => 'failed',
            'Refunded' => 'refunded',
            'Cancelled' => 'cancelled',
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
            Log::info("[Stone][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
