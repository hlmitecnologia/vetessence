<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
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
        $this->log('Iniciando cobrança PDV (Mercado Pago Point)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiChargePoint($invoice);
        }

        return $this->simulatedChargePoint($invoice);
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

        $type = $payload['type'] ?? '';

        if ($type === 'order') {
            $orderId = $payload['data']['id'] ?? null;
            if ($orderId) {
                return $this->verifyOrderWebhook($orderId);
            }
        }

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
        return ['portal', 'pdv'];
    }

    public function queryTransaction(string $identifier): array
    {
        return $this->queryOrder($identifier);
    }

    public function cancelTransaction(string $identifier): bool
    {
        return $this->cancelOrder($identifier);
    }

    public function queryOrder(string $orderId): array
    {
        if (!$this->hasCredentials()) {
            return $this->simulatedQueryOrder($orderId);
        }

        if (!$this->setupMercadoPago()) {
            return ['success' => false, 'status' => 'failed', 'message' => 'Mercado Pago não configurado.'];
        }

        try {
            $client = $this->makeHttpClient();
            $response = $client->get("/v1/orders/{$orderId}");
            $body = json_decode((string) $response->getBody(), true);

            $status = $body['status'] ?? 'created';
            $mappedStatus = $this->mapOrderStatus($status);

            $result = [
                'success' => true,
                'status' => $mappedStatus,
                'raw_response' => $body,
            ];

            if ($mappedStatus === 'paid') {
                $payment = $body['transactions']['payments'][0] ?? null;
                $result['payment_method'] = $this->mapMpPaymentType($payment['payment_method']['type'] ?? 'credit_card');
                $result['transaction_data'] = [
                    'payment_id' => $payment['id'] ?? null,
                    'amount' => $payment['amount'] ?? null,
                    'status_detail' => $payment['status_detail'] ?? null,
                ];
            } elseif ($mappedStatus === 'pending') {
                $result['message'] = 'Aguardando pagamento no Point Smart...';
            } else {
                $result['message'] = 'Status: ' . $status;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro ao consultar order', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            if ($this->gateway->is_sandbox) {
                Log::info('[MercadoPago][SANDBOX] Query falhou, usando modo simulado');
                return $this->simulatedQueryOrder($orderId);
            }

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Erro ao consultar order: ' . $e->getMessage(),
            ];
        }
    }

    protected function cancelOrder(string $orderId): bool
    {
        if (!$this->hasCredentials()) {
            return true;
        }

        if (!$this->setupMercadoPago()) {
            return false;
        }

        try {
            $client = $this->makeHttpClient();
            $client->post("/v1/orders/{$orderId}/cancel", [
                'headers' => [
                    'X-Idempotency-Key' => (string) Str::uuid(),
                ],
            ]);
            Log::info('[MercadoPago] Order cancelada', ['order_id' => $orderId]);
            return true;
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro ao cancelar order', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            if ($this->gateway->is_sandbox) {
                Log::info('[MercadoPago][SANDBOX] Cancel falhou, aceitando como cancelado');
                return true;
            }

            return false;
        }
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

    protected function apiChargePoint(Invoice $invoice, int $retryCount = 0): array
    {
        if (!$this->setupMercadoPago()) {
            return $this->errorResponse('Mercado Pago não configurado: access_token ausente.');
        }

        $terminalId = $this->gateway->config['terminal_id'] ?? null;

        if (!$terminalId && !$this->gateway->is_sandbox) {
            return $this->errorResponse('Terminal ID não configurado. Configure o ID do Point Smart nas credenciais do gateway.');
        }

        if (!$terminalId && $this->gateway->is_sandbox) {
            $terminalId = 'PAX_A910__SBX0000001';
        }

        try {
            $client = $this->makeHttpClient();

            $this->cancelPendingOrdersOnTerminal($client, $terminalId);

            $idempotencyKey = (string) Str::uuid();
            $orderId = 'VET-' . $invoice->id . '-' . strtoupper(uniqid());

            $payload = [
                'type' => 'point',
                'external_reference' => (string) $invoice->id,
                'description' => 'Fatura #' . $invoice->invoice_number,
                'expiration_time' => 'PT15M',
                'transactions' => [
                    'payments' => [
                        [
                            'amount' => number_format($invoice->total, 2, '.', ''),
                        ],
                    ],
                ],
                'config' => [
                    'point' => [
                        'terminal_id' => $terminalId,
                        'print_on_terminal' => 'no_ticket',
                    ],
                    'payment_method' => [
                        'default_type' => 'credit_card',
                        'default_installments' => 1,
                        'installments_cost' => 'seller',
                    ],
                ],
            ];

            $response = $client->post('/v1/orders', [
                'json' => $payload,
                'headers' => [
                    'X-Idempotency-Key' => $idempotencyKey,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (empty($body['id'])) {
                throw new \RuntimeException('Resposta inválida ao criar order Point.');
            }

            $mpOrderId = $body['id'];

            if ($this->gateway->is_sandbox) {
                $this->simulateOrderStatus($mpOrderId, 'processed');

                $queryResult = $this->queryOrder($mpOrderId);

                if ($queryResult['success'] && ($queryResult['status'] ?? '') === 'paid') {
                    return [
                        'success' => true,
                        'transaction_id' => $mpOrderId,
                        'reference' => (string) $invoice->id,
                        'status' => 'paid',
                        'message' => '[SANDBOX] Pagamento simulado com sucesso.',
                        'payment_method' => $queryResult['payment_method'] ?? 'cartao_credito',
                        'transaction_data' => $queryResult['transaction_data'] ?? [],
                        'redirect_url' => null,
                        'raw_response' => $body,
                    ];
                }
            }

            return [
                'success' => true,
                'transaction_id' => $mpOrderId,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Pagamento enviado ao Point Smart. Aguardando...',
                'redirect_url' => null,
                'raw_response' => $body,
            ];
        } catch (MPApiException $e) {
            $statusCode = $e->getApiResponse()->getStatusCode();
            $content = $e->getApiResponse()->getContent();

            if ($statusCode === 409 && $this->isAlreadyQueuedError($content) && $retryCount < 1) {
                Log::info('[MercadoPago] Order pendente detectada no terminal, cancelando e tentando novamente');
                $client = $this->makeHttpClient();
                $this->cancelPendingOrdersOnTerminal($client, $terminalId);

                return $this->apiChargePoint($invoice, $retryCount + 1);
            }

            Log::warning('[MercadoPago] Erro na API ao criar order Point', [
                'status' => $statusCode,
                'response' => $content,
            ]);

            if ($this->gateway->is_sandbox) {
                Log::info('[MercadoPago][SANDBOX] API Point indisponível, usando modo simulado');
                return $this->simulatedChargePoint($invoice);
            }

            return $this->errorResponse('Erro Mercado Pago: ' . $this->parseApiErrorMessage($content));
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro inesperado ao criar order Point', ['error' => $e->getMessage()]);

            if ($this->gateway->is_sandbox) {
                Log::info('[MercadoPago][SANDBOX] Erro inesperado, usando modo simulado');
                return $this->simulatedChargePoint($invoice);
            }

            return $this->errorResponse('Erro inesperado: ' . $e->getMessage());
        }
    }

    protected function simulateOrderStatus(string $orderId, string $status): bool
    {
        try {
            $client = $this->makeHttpClient();
            $client->post("/v1/orders/{$orderId}/events", [
                'json' => ['status' => $status],
            ]);
            Log::info('[MercadoPago][SANDBOX] Order simulada', ['order_id' => $orderId, 'status' => $status]);
            return true;
        } catch (\Exception $e) {
            Log::error('[MercadoPago][SANDBOX] Erro ao simular status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function simulatedChargePoint(Invoice $invoice): array
    {
        $orderId = 'MP-POINT-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $orderId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Pagamento enviado ao Point Smart. Aguardando...',
            'redirect_url' => null,
            'raw_response' => [
                'id' => $orderId,
                'status' => 'created',
                'simulated' => true,
            ],
        ];
    }

    protected function simulatedQueryOrder(string $orderId): array
    {
        return [
            'success' => true,
            'status' => 'paid',
            'payment_method' => 'cartao_credito',
            'transaction_data' => [
                'payment_id' => 'MP-PAY-' . strtoupper(uniqid()),
                'amount' => '0.00',
                'status_detail' => 'accredited',
            ],
            'raw_response' => [
                'id' => $orderId,
                'status' => 'processed',
                'simulated' => true,
            ],
        ];
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

            $redirectUrl = $preference->init_point;
            if ($this->gateway->is_sandbox && !empty($preference->sandbox_init_point)) {
                $redirectUrl = $preference->sandbox_init_point;
            }

            return [
                'success' => true,
                'transaction_id' => $preference->id,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Checkout Mercado Pago criado.',
                'redirect_url' => $redirectUrl,
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

    protected function verifyOrderWebhook(string $orderId): ?array
    {
        if (!$this->hasCredentials()) {
            return $this->simulatedVerifyOrderWebhook($orderId);
        }

        return $this->apiVerifyOrderWebhook($orderId);
    }

    protected function apiVerifyOrderWebhook(string $orderId): ?array
    {
        if (!$this->setupMercadoPago()) {
            Log::warning('[MercadoPago] Order webhook ignorado — access_token ausente.');
            return null;
        }

        try {
            $client = $this->makeHttpClient();
            $response = $client->get("/v1/orders/{$orderId}");
            $body = json_decode((string) $response->getBody(), true);

            $status = $body['status'] ?? 'created';
            $mappedStatus = $this->mapOrderStatus($status);
            $externalReference = $body['external_reference'] ?? null;
            $payment = $body['transactions']['payments'][0] ?? null;

            return [
                'transaction_id' => $orderId,
                'reference' => $externalReference,
                'status' => $mappedStatus,
                'paid_at' => $mappedStatus === 'paid' ? ($body['last_updated_date'] ?? now()->toIso8601String()) : null,
                'gateway_status' => $status,
                'payment_method' => $mappedStatus === 'paid' ? $this->mapMpPaymentType($payment['payment_method']['type'] ?? 'credit_card') : null,
            ];
        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro ao consultar order no webhook', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function simulatedVerifyOrderWebhook(string $orderId): ?array
    {
        return [
            'transaction_id' => $orderId,
            'reference' => null,
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_status' => 'processed',
            'payment_method' => 'cartao_credito',
        ];
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

    protected function mapOrderStatus(?string $mpStatus): string
    {
        return match ($mpStatus) {
            'processed' => 'paid',
            'failed' => 'failed',
            'canceled' => 'cancelled',
            'refunded' => 'refunded',
            'expired' => 'failed',
            'created' => 'pending',
            'at_terminal' => 'pending',
            'action_required' => 'pending',
            default => 'pending',
        };
    }

    protected function mapMpPaymentType(?string $mpType): string
    {
        return match ($mpType) {
            'credit_card' => 'cartao_credito',
            'debit_card' => 'cartao_debito',
            'pix' => 'pix',
            'cash' => 'dinheiro',
            default => 'cartao_credito',
        };
    }

    protected function cancelPendingOrdersOnTerminal(Client $client, ?string $terminalId): void
    {
        $pendingInvoices = Invoice::where('gateway_id', $this->gateway->id)
            ->where('status', 'pending')
            ->whereNotNull('gateway_transaction_id')
            ->where('gateway_transaction_id', 'like', 'ORD%')
            ->get();

        foreach ($pendingInvoices as $pendingInvoice) {
            $orderId = $pendingInvoice->gateway_transaction_id;
            try {
                $client->post("/v1/orders/{$orderId}/cancel", [
                    'headers' => [
                        'X-Idempotency-Key' => (string) Str::uuid(),
                    ],
                ]);
                Log::info('[MercadoPago] Order pendente cancelada', [
                    'order_id' => $orderId,
                    'invoice_id' => $pendingInvoice->id,
                ]);
                $pendingInvoice->update([
                    'gateway_status' => 'cancelled',
                    'gateway_transaction_id' => null,
                ]);
            } catch (\Exception $e) {
                Log::warning('[MercadoPago] Falha ao cancelar order pendente', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function isAlreadyQueuedError(string $content): bool
    {
        return str_contains($content, 'already_queued_order_on_terminal');
    }

    protected function parseApiErrorMessage(string $content): string
    {
        $decoded = json_decode($content, true);
        if (!empty($decoded['errors'][0]['message'])) {
            return $decoded['errors'][0]['message'];
        }
        if (!empty($decoded['message'])) {
            return $decoded['message'];
        }
        return 'erro desconhecido';
    }

    protected function makeHttpClient(): Client
    {
        return new Client([
            'base_uri' => 'https://api.mercadopago.com',
            'timeout' => 15,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->gateway->secret_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
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
