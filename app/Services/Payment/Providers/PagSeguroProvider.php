<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;
use PagSeguro\Configuration\Configure;
use PagSeguro\Domains\AccountCredentials;
use PagSeguro\Domains\Requests\Payment;
use PagSeguro\Services\Checkout\Payment as CheckoutPayment;
use PagSeguro\Services\Transactions\Search\Code as TransactionSearch;

class PagSeguroProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (maquininha PagSeguro)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCharge($invoice);
        }

        return $this->simulatedCharge($invoice);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (PagSeguro)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCheckout($invoice);
        }

        return $this->simulatedCheckout($invoice);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook PagSeguro', ['payload' => $payload]);

        $notificationCode = $payload['notificationCode'] ?? $payload['notification_code'] ?? null;

        if (!$notificationCode) {
            Log::warning('PagSeguro: webhook sem notificationCode', $payload);
            return null;
        }

        if ($this->hasCredentials()) {
            return $this->apiVerifyWebhook($notificationCode, $payload);
        }

        return $this->simulatedVerifyWebhook($notificationCode, $payload);
    }

    public static function supportedChannels(): array
    {
        return ['portal', 'pdv', 'both'];
    }

    protected function hasCredentials(): bool
    {
        return !empty($this->gateway->public_key) && !empty($this->gateway->secret_key);
    }

    protected function setupPagSeguro(): bool
    {
        if (!$this->hasCredentials()) {
            return false;
        }

        Configure::setAccountCredentials($this->gateway->public_key, $this->gateway->secret_key);
        Configure::setEnvironment($this->gateway->is_sandbox ? 'sandbox' : 'production');
        Configure::setCharset('UTF-8');
        Configure::setLog(false, '');

        return true;
    }

    protected function simulatedCharge(Invoice $invoice): array
    {
        $transactionId = 'PS-PDV-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Cobrança iniciada na maquininha PagSeguro Pro. Aguardando pagamento...',
            'redirect_url' => null,
            'raw_response' => [
                'code' => $transactionId,
                'status' => 'pending',
                'simulated' => true,
            ],
        ];
    }

    protected function apiCharge(Invoice $invoice): array
    {
        return $this->errorResponse('PagSeguro PDV (maquininha) requer SDK front-end para tokenização de cartão. Implemente o PagSeguro Pro via WebPOS ou transação manual.');
    }

    protected function simulatedCheckout(Invoice $invoice): array
    {
        $transactionId = 'PS-CK-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Checkout PagSeguro criado.',
            'redirect_url' => 'https://sandbox.pagseguro.uol.com.br/checkout/v2/cart.html?' . http_build_query([
                'code' => $transactionId,
            ]),
            'raw_response' => [
                'code' => $transactionId,
                'redirect_url' => 'https://sandbox.pagseguro.uol.com.br/checkout/...',
                'simulated' => true,
            ],
        ];
    }

    protected function apiCheckout(Invoice $invoice): array
    {
        if (!$this->setupPagSeguro()) {
            return $this->errorResponse('PagSeguro não configurado: credenciais ausentes.');
        }

        try {
            $payment = new Payment();
            $payment->setCurrency('BRL');
            $payment->setReference((string) $invoice->id);

            foreach ($invoice->items as $item) {
                $payment->addItems()->withParameters(
                    (string) $item->id,
                    strip_tags($item->description),
                    (int) $item->quantity,
                    (float) $item->unit_price,
                );
            }

            if ($invoice->tutor) {
                $payment->setSender()->setName($invoice->tutor->name ?? 'Cliente');
                $payment->setSender()->setEmail($invoice->tutor->email ?? 'cliente@email.com');
            }

            $payment->setRedirectUrl(route('portal.invoices.show', $invoice->id));
            $payment->setNotificationUrl($this->gateway->webhook_url);

            $credentials = new AccountCredentials(
                $this->gateway->public_key,
                $this->gateway->secret_key,
            );

            $url = CheckoutPayment::checkout($credentials, $payment, false);

            return [
                'success' => true,
                'transaction_id' => $payment->getReference(),
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Checkout PagSeguro criado.',
                'redirect_url' => $url,
                'raw_response' => ['redirect_url' => $url],
            ];
        } catch (\Exception $e) {
            Log::error('[PagSeguro] Erro ao criar checkout', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro PagSeguro: ' . $e->getMessage());
        }
    }

    protected function simulatedVerifyWebhook(string $notificationCode, array $payload): ?array
    {
        $invoiceId = $payload['reference'] ?? $payload['data']['id'] ?? null;

        return [
            'transaction_id' => $notificationCode,
            'reference' => $invoiceId,
            'status' => $invoiceId ? 'paid' : 'pending',
            'paid_at' => $invoiceId ? now() : null,
            'gateway_status' => $invoiceId ? 'approved' : 'pending',
            'raw_response' => $payload,
        ];
    }

    protected function apiVerifyWebhook(string $notificationCode, array $payload): ?array
    {
        if (!$this->setupPagSeguro()) {
            Log::warning('[PagSeguro] Webhook ignorado — credenciais ausentes.');
            return null;
        }

        try {
            $credentials = new AccountCredentials(
                $this->gateway->public_key,
                $this->gateway->secret_key,
            );

            $transaction = TransactionSearch::search($credentials, $notificationCode);

            $statusCode = $transaction->getStatus();

            $statusMap = [
                1 => 'pending',
                2 => 'pending',
                3 => 'paid',
                4 => 'paid',
                5 => 'pending',
                6 => 'refunded',
                7 => 'cancelled',
            ];

            return [
                'transaction_id' => $transaction->getCode(),
                'reference' => $transaction->getReference(),
                'status' => $statusMap[$statusCode] ?? 'pending',
                'paid_at' => in_array($statusCode, [3, 4]) ? new \DateTime($transaction->getDate()) : null,
                'gateway_status' => (string) $statusCode,
                'raw_response' => [
                    'code' => $transaction->getCode(),
                    'status' => $statusCode,
                    'reference' => $transaction->getReference(),
                ],
            ];
        } catch (\Exception $e) {
            Log::warning('[PagSeguro] Erro ao consultar notificação', [
                'notificationCode' => $notificationCode,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
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
            Log::info("[PagSeguro][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
