<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MultiplusCardProvider implements PaymentGatewayProvider
{
    private const BASE_URL = 'https://api.pinpdv.com.br';

    private const TIPO_PAGAMENTO_MAP = [
        0 => 'none',
        1 => 'dinheiro',
        2 => 'cartao_credito',
        3 => 'cartao_debito',
        4 => 'pix',
        6 => 'vale_refeicao',
        7 => 'vale_alimentacao',
    ];

    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (MultiplusCard PinPDV)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCharge($invoice);
        }

        return $this->simulatedCharge($invoice);
    }

    public function checkout(Invoice $invoice): array
    {
        return [
            'success' => false,
            'message' => 'MultiplusCard PinPDV não suporta checkout online. Use o canal PDV.',
            'transaction_id' => null,
            'status' => 'failed',
            'redirect_url' => null,
            'raw_response' => [],
        ];
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        return null;
    }

    public static function supportedChannels(): array
    {
        return ['pdv'];
    }

    public function queryPreVenda(string $identifier): array
    {
        if (!$this->hasCredentials()) {
            return $this->simulatedQueryPreVenda($identifier);
        }

        try {
            $client = $this->makeClient();
            $response = $client->get("/pre-venda/{$identifier}");
            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                throw new \RuntimeException('Resposta inválida da API MultiplusCard.');
            }

            $statusKey = $body['status']['key'] ?? null;

            if ($statusKey === 2) {
                $transacao = $body['transacoes'][0] ?? null;
                $dados = $transacao['dados'] ?? null;
                $tipoPagamento = $transacao['tipoPagamento'] ?? 0;

                return [
                    'success' => true,
                    'status' => 'paid',
                    'payment_method' => self::TIPO_PAGAMENTO_MAP[$tipoPagamento] ?? 'cartao_credito',
                    'transaction_data' => [
                        'nsu' => $dados['nsu'] ?? null,
                        'autorizacao' => $dados['autorizacao'] ?? null,
                        'bandeira' => $dados['bandeira'] ?? null,
                        'adquirente' => $dados['adquirente'] ?? null,
                        'data_hora' => $dados['dataHora'] ?? null,
                    ],
                    'raw_response' => $body,
                ];
            }

            if ($statusKey === 0 || $statusKey === 1) {
                return [
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'Aguardando pagamento no SmartPOS...',
                    'raw_response' => $body,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Status desconhecido: ' . ($body['status']['value'] ?? 'N/A'),
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[MultiplusCard] Erro ao consultar pré-venda', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Erro ao consultar pré-venda: ' . $e->getMessage(),
            ];
        }
    }

    public function abortPreVenda(string $identifier): bool
    {
        if (!$this->hasCredentials()) {
            return true;
        }

        try {
            $client = $this->makeClient();
            $client->delete("/pre-venda/{$identifier}");
            return true;
        } catch (\Exception $e) {
            Log::warning('[MultiplusCard] Erro ao abortar pré-venda', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function listDevices(): array
    {
        if (!$this->hasCredentials()) {
            return ['data' => []];
        }

        try {
            $client = $this->makeClient();
            $response = $client->get('/pinpdv');
            return json_decode((string) $response->getBody(), true) ?? ['data' => []];
        } catch (\Exception $e) {
            Log::error('[MultiplusCard] Erro ao listar dispositivos', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    protected function hasCredentials(): bool
    {
        return !empty($this->gateway->secret_key) && !empty($this->gateway->config['pinpdv_id']);
    }

    protected function simulatedCharge(Invoice $invoice): array
    {
        $identifier = 'MC-PDV-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $identifier,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Pré-venda enviada para o SmartPOS MultiplusCard. Aguardando pagamento...',
            'redirect_url' => null,
            'raw_response' => [
                'identificador' => $identifier,
                'status' => ['key' => 0, 'value' => 'Aguardando'],
                'simulated' => true,
            ],
        ];
    }

    protected function simulatedQueryPreVenda(string $identifier): array
    {
        return [
            'success' => true,
            'status' => 'paid',
            'payment_method' => 'cartao_credito',
            'transaction_data' => [
                'nsu' => str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT),
                'autorizacao' => str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'bandeira' => 'VISA',
                'adquirente' => 'MULTICARD',
                'data_hora' => now()->toIso8601String(),
            ],
            'raw_response' => [
                'identificador' => $identifier,
                'status' => ['key' => 2, 'value' => 'Concluido'],
                'simulated' => true,
            ],
        ];
    }

    protected function apiCharge(Invoice $invoice): array
    {
        try {
            $client = $this->makeClient();
            $identifier = $invoice->gateway_transaction_id ?: ('VET-' . $invoice->id . '-' . strtoupper(uniqid()));
            $payload = [
                'Identificador' => $identifier,
                'Valor' => (float) number_format($invoice->total, 2, '.', ''),
                'Descricao' => 'Fatura #' . $invoice->invoice_number,
                'Parcelas' => 1,
                'TipoPagamento' => 0,
                'PinPdvId' => (int) $this->gateway->config['pinpdv_id'],
                'Produtos' => [],
            ];

            $response = $client->post('/pre-venda', ['json' => $payload]);
            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body) || empty($body['id'])) {
                throw new \RuntimeException('Resposta inválida ao criar pré-venda.');
            }

            return [
                'success' => true,
                'transaction_id' => $identifier,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Pré-venda enviada para o SmartPOS. Aguardando pagamento...',
                'redirect_url' => null,
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[MultiplusCard] Erro ao criar pré-venda', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'transaction_id' => null,
                'reference' => (string) $invoice->id,
                'status' => 'failed',
                'message' => 'Erro MultiplusCard: ' . $e->getMessage(),
                'redirect_url' => null,
                'raw_response' => [],
            ];
        }
    }

    protected function makeClient(): Client
    {
        return new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 15,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->gateway->secret_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function log(string $message, mixed $context = []): void
    {
        if ($this->gateway->is_sandbox) {
            Log::info("[MultiplusCard][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
