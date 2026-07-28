<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PayerProvider implements PaymentGatewayProvider
{
    protected const AUTH_URL = 'https://bk07exvx19.execute-api.us-east-1.amazonaws.com/dev-stage/oauth/login';
    protected const BASE_URL = 'https://ms7bi3gsxk.execute-api.us-east-1.amazonaws.com/prod-stage/cloud-notification';

    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (Payer)', $invoice);

        if (!$this->hasConfig()) {
            return $this->errorResponse('Payer não configurado: campos obrigatórios ausentes.');
        }

        return $this->apiChargePoint($invoice);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Payer)', $invoice);

        if (!$this->hasConfig()) {
            return $this->errorResponse('Payer não configurado: campos obrigatórios ausentes.');
        }

        return $this->apiCheckout($invoice);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Payer', ['payload' => $payload]);

        $statusTransaction = $payload['statusTransaction'] ?? null;
        $correlationId = $payload['correlationId'] ?? null;
        $idPayer = $payload['idPayer'] ?? null;

        if (!$correlationId) {
            Log::warning('Payer: webhook sem correlationId', $payload);
            return null;
        }

        $mappedStatus = $this->mapStatus($statusTransaction);

        return [
            'transaction_id' => $idPayer ?: $correlationId,
            'reference' => null,
            'correlation_id' => $correlationId,
            'status' => $mappedStatus,
            'paid_at' => $mappedStatus === 'paid' ? ($payload['transactionDateTime'] ?? now()->toIso8601String()) : null,
            'gateway_status' => $statusTransaction,
            'payment_method' => $this->mapPaymentMethod($payload['paymentMethod'] ?? null),
            'raw_response' => $payload,
        ];
    }

    public static function supportedChannels(): array
    {
        return ['portal', 'pdv', 'both'];
    }

    public function queryTransaction(string $identifier): array
    {
        return $this->queryOrder($identifier);
    }

    public function cancelTransaction(string $identifier): bool
    {
        return $this->cancelOrder($identifier);
    }

    protected function apiChargePoint(Invoice $invoice): array
    {
        $token = $this->getAuthToken();
        if (!$token) {
            return $this->errorResponse('Payer: falha na autenticação. Verifique as credenciais.');
        }

        try {
            $client = $this->makeHttpClient($token);
            $correlationId = (string) Str::uuid();

            $validatePayload = $this->buildPaymentPayload($invoice, $correlationId, 'SYNC');

            $validateResponse = $client->post(self::BASE_URL . '/validate-webhook', [
                'json' => $validatePayload,
            ]);

            $validateBody = json_decode((string) $validateResponse->getBody(), true);
            if (!empty($validateBody['error'])) {
                $errors = implode(', ', $validateBody['errorList'] ?? ['Erro desconhecido']);
                return $this->errorResponse("Payer: validação falhou — {$errors}");
            }

            $callbackUrl = route('webhooks.payer', ['gateway' => $this->gateway->id]);

            $executePayload = $validatePayload;
            $executePayload['data']['callbackUrl'] = $callbackUrl;

            $executeResponse = $client->post(self::BASE_URL . '/create', [
                'json' => $executePayload,
            ]);

            $executeBody = json_decode((string) $executeResponse->getBody(), true);

            return [
                'success' => true,
                'transaction_id' => $correlationId,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Pagamento enviado ao terminal Payer. Aguardando...',
                'redirect_url' => null,
                'raw_response' => $executeBody,
            ];
        } catch (\Exception $e) {
            Log::error('[Payer] Erro ao criar ordem de pagamento', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro Payer: ' . $e->getMessage());
        }
    }

    protected function apiCheckout(Invoice $invoice): array
    {
        $token = $this->getAuthToken();
        if (!$token) {
            return $this->errorResponse('Payer: falha na autenticação.');
        }

        try {
            $client = $this->makeHttpClient($token);
            $correlationId = (string) Str::uuid();

            $callbackUrl = route('webhooks.payer', ['gateway' => $this->gateway->id]);

            $payload = [
                'type' => 'INPUT',
                'origin' => 'VETESSENCE',
                'data' => [
                    'callbackUrl' => $callbackUrl,
                    'correlationId' => $correlationId,
                    'flow' => 'ASYNC',
                    'automationName' => $this->gateway->config['automation_name'],
                    'receiver' => [
                        'companyId' => $this->gateway->config['company_id'],
                        'storeId' => $this->gateway->config['store_id'],
                        'terminalId' => $this->gateway->config['terminal_id'],
                    ],
                    'message' => [
                        'command' => 'PAYMENT',
                        'paymentMethod' => 'GENERIC_LINK',
                        'value' => (float) $invoice->total,
                    ],
                ],
            ];

            $client->post(self::BASE_URL . '/validate-webhook', [
                'json' => $payload,
            ]);

            $executeResponse = $client->post(self::BASE_URL . '/create', [
                'json' => $payload,
            ]);

            $body = json_decode((string) $executeResponse->getBody(), true);

            return [
                'success' => true,
                'transaction_id' => $correlationId,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Link de pagamento Payer criado.',
                'redirect_url' => null,
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[Payer] Erro ao criar checkout', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro Payer: ' . $e->getMessage());
        }
    }

    protected function queryOrder(string $correlationId): array
    {
        $token = $this->getAuthToken();
        if (!$token) {
            return ['success' => false, 'status' => 'failed', 'message' => 'Payer: falha na autenticação.'];
        }

        try {
            $client = $this->makeHttpClient($token);
            $automationName = $this->gateway->config['automation_name'];

            $response = $client->get(self::BASE_URL . "/order/{$correlationId}", [
                'query' => ['automationName' => $automationName],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            $orderStatus = $body['status'] ?? 'PENDING';
            $output = $body['receivedOutput']['data']['message'] ?? null;
            $transactionStatus = $output['statusTransaction'] ?? null;

            $mappedStatus = $this->mapStatus($transactionStatus ?? $orderStatus);

            $result = [
                'success' => true,
                'status' => $mappedStatus,
                'raw_response' => $body,
            ];

            if ($mappedStatus === 'paid' && $output) {
                $result['payment_method'] = $this->mapPaymentMethod($output['paymentMethod'] ?? null);
                $result['transaction_data'] = [
                    'id_payer' => $output['idPayer'] ?? null,
                    'amount' => $output['value'] ?? null,
                    'status_detail' => $transactionStatus,
                    'third_party_id' => $output['thirdPartyId'] ?? null,
                ];
            } elseif ($mappedStatus === 'pending') {
                $result['message'] = 'Aguardando pagamento no terminal Payer...';
            } else {
                $result['message'] = 'Status: ' . $orderStatus;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('[Payer] Erro ao consultar ordem', [
                'correlation_id' => $correlationId,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'status' => 'failed', 'message' => 'Erro ao consultar: ' . $e->getMessage()];
        }
    }

    protected function cancelOrder(string $correlationId): bool
    {
        $token = $this->getAuthToken();
        if (!$token) {
            return false;
        }

        try {
            $client = $this->makeHttpClient($token);

            $queryResult = $this->queryOrder($correlationId);
            $idPayer = $queryResult['raw_response']['receivedOutput']['data']['message']['idPayer'] ?? null;

            if (!$idPayer) {
                Log::warning('[Payer] Não foi possível obter idPayer para cancelar', ['correlation_id' => $correlationId]);
                return false;
            }

            $validatePayload = [
                'type' => 'INPUT',
                'origin' => 'VETESSENCE',
                'data' => [
                    'callbackUrl' => route('webhooks.payer', ['gateway' => $this->gateway->id]),
                    'correlationId' => (string) Str::uuid(),
                    'flow' => 'SYNC',
                    'automationName' => $this->gateway->config['automation_name'],
                    'receiver' => [
                        'companyId' => $this->gateway->config['company_id'],
                        'storeId' => $this->gateway->config['store_id'],
                        'terminalId' => $this->gateway->config['terminal_id'],
                    ],
                    'message' => [
                        'command' => 'CANCELLMENT',
                        'idPayer' => $idPayer,
                    ],
                ],
            ];

            $client->post(self::BASE_URL . '/validate-webhook', [
                'json' => $validatePayload,
            ]);

            $client->post(self::BASE_URL . '/create', [
                'json' => $validatePayload,
            ]);

            Log::info('[Payer] Cancelamento enviado', ['correlation_id' => $correlationId, 'id_payer' => $idPayer]);
            return true;
        } catch (\Exception $e) {
            Log::error('[Payer] Erro ao cancelar', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function buildPaymentPayload(Invoice $invoice, string $correlationId, string $flow = 'SYNC'): array
    {
        $config = $this->gateway->config;

        return [
            'type' => 'INPUT',
            'origin' => 'VETESSENCE',
            'data' => [
                'callbackUrl' => '',
                'correlationId' => $correlationId,
                'flow' => $flow,
                'automationName' => $config['automation_name'],
                'receiver' => [
                    'companyId' => $config['company_id'],
                    'storeId' => $config['store_id'],
                    'terminalId' => $config['terminal_id'],
                ],
                'message' => [
                    'command' => 'PAYMENT',
                    'value' => (float) $invoice->total,
                ],
            ],
        ];
    }

    protected function getAuthToken(): ?string
    {
        $cacheKey = 'payer_auth_token_' . $this->gateway->id;

        return Cache::remember($cacheKey, now()->addHours(20), function () {
            return $this->fetchAuthToken();
        });
    }

    protected function fetchAuthToken(): ?string
    {
        try {
            $client = new Client(['timeout' => 15]);

            $response = $client->post(self::AUTH_URL, [
                'json' => [
                    'clientId' => $this->gateway->config['client_id'],
                    'userName' => $this->gateway->config['username'],
                    'userAlias' => $this->gateway->config['user_alias'],
                ],
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            $idToken = $body['AuthenticationResult']['IdToken'] ?? null;

            if (!$idToken) {
                Log::error('[Payer] Auth response sem IdToken', ['response' => $body]);
                return null;
            }

            Log::info('[Payer] Token obtido com sucesso');
            return $idToken;
        } catch (\Exception $e) {
            Log::error('[Payer] Erro na autenticação', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function hasConfig(): bool
    {
        $config = $this->gateway->config ?? [];

        return !empty($config['company_id'])
            && !empty($config['store_id'])
            && !empty($config['terminal_id'])
            && !empty($config['automation_name'])
            && !empty($config['client_id'])
            && !empty($config['username'])
            && !empty($config['user_alias']);
    }

    protected function mapStatus(?string $payerStatus): string
    {
        return match ($payerStatus) {
            'APPROVED' => 'paid',
            'REJECTED' => 'failed',
            'ABORTED' => 'cancelled',
            'CANCELLED' => 'cancelled',
            'DECLINED' => 'failed',
            'PENDING' => 'pending',
            'PROCESSING' => 'pending',
            'COMPLETED' => 'pending',
            default => 'pending',
        };
    }

    protected function mapPaymentMethod(?string $method): ?string
    {
        return match ($method) {
            'CARD' => 'cartao_credito',
            'CASH' => 'dinheiro',
            'PIX' => 'pix',
            'WALLET' => 'carteira_digital',
            default => null,
        };
    }

    protected function makeHttpClient(?string $token = null): Client
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return new Client([
            'base_uri' => '',
            'timeout' => 15,
            'headers' => $headers,
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
        Log::info("[Payer] {$message}", is_array($context) ? $context : ['context' => $context]);
    }
}
