<?php

namespace App\Services\Payment\Providers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeProvider implements PaymentGatewayProvider
{
    public function __construct(
        protected PaymentGateway $gateway,
    ) {}

    public function charge(Invoice $invoice): array
    {
        $this->log('Iniciando cobrança PDV (Stripe Terminal)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCharge($invoice);
        }

        return $this->simulatedCharge($invoice);
    }

    public function checkout(Invoice $invoice): array
    {
        $this->log('Criando checkout online (Stripe)', $invoice);

        if ($this->hasCredentials()) {
            return $this->apiCheckout($invoice);
        }

        return $this->simulatedCheckout($invoice);
    }

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array
    {
        $this->log('Verificando webhook Stripe', ['payload' => $payload]);

        $transactionId = $payload['data']['object']['id'] ?? $payload['id'] ?? $payload['transaction_id'] ?? null;

        if (!$transactionId) {
            Log::warning('Stripe: webhook sem transaction_id', $payload);
            return null;
        }

        if ($this->hasCredentials()) {
            return $this->apiVerifyWebhook($transactionId, $payload);
        }

        return $this->simulatedVerifyWebhook($transactionId, $payload);
    }

    public static function supportedChannels(): array
    {
        return ['portal']; // Stripe não suporta maquininha no Brasil
    }

    protected function hasCredentials(): bool
    {
        return !empty($this->gateway->secret_key);
    }

    protected function setupStripe(): bool
    {
        if (!$this->hasCredentials()) {
            return false;
        }

        Stripe::setApiKey($this->gateway->secret_key);
        return true;
    }

    protected function simulatedCharge(Invoice $invoice): array
    {
        return [
            'success' => false,
            'transaction_id' => null,
            'reference' => null,
            'status' => 'failed',
            'message' => 'Stripe não suporta PDV/maquininha no Brasil. Utilize Stripe Terminal somente se configurado para sua região.',
            'redirect_url' => null,
            'raw_response' => [],
        ];
    }

    protected function apiCharge(Invoice $invoice): array
    {
        return $this->simulatedCharge($invoice);
    }

    protected function simulatedCheckout(Invoice $invoice): array
    {
        $transactionId = 'STRIPE-CK-' . strtoupper(uniqid());

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference' => (string) $invoice->id,
            'status' => 'pending',
            'message' => '[SIMULADO] Checkout Stripe criado.',
            'redirect_url' => 'https://checkout.stripe.com/pay/' . $transactionId,
            'raw_response' => [
                'id' => $transactionId,
                'url' => 'https://checkout.stripe.com/pay/' . $transactionId,
                'simulated' => true,
            ],
        ];
    }

    protected function apiCheckout(Invoice $invoice): array
    {
        if (!$this->setupStripe()) {
            return $this->errorResponse('Stripe não configurado: secret_key ausente.');
        }

        try {
            $lineItems = [];
            foreach ($invoice->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => strip_tags($item->description ?? 'Item'),
                        ],
                        'unit_amount' => (int) round(($item->unit_price ?? 0) * 100),
                    ],
                    'quantity' => max(1, (int) ($item->quantity ?? 1)),
                ];
            }

            if (empty($lineItems)) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'Fatura ' . $invoice->invoice_number,
                        ],
                        'unit_amount' => (int) round($invoice->total * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            $session = Session::create([
                'mode' => 'payment',
                'line_items' => $lineItems,
                'success_url' => route('portal.invoices.show', $invoice->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('portal.invoices.show', $invoice->id),
                'metadata' => [
                    'invoice_id' => (string) $invoice->id,
                ],
            ]);

            return [
                'success' => true,
                'transaction_id' => $session->id,
                'reference' => (string) $invoice->id,
                'status' => 'pending',
                'message' => 'Checkout Stripe criado.',
                'redirect_url' => $session->url,
                'raw_response' => $session->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('[Stripe] Erro na API ao criar checkout', [
                'error' => $e->getMessage(),
                'code' => $e->getHttpStatus(),
            ]);
            return $this->errorResponse('Erro Stripe: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('[Stripe] Erro inesperado ao criar checkout', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro inesperado: ' . $e->getMessage());
        }
    }

    protected function simulatedVerifyWebhook(string $transactionId, array $payload): ?array
    {
        $eventType = $payload['type'] ?? '';

        if (str_starts_with($eventType, 'checkout.session.completed')
            || str_starts_with($eventType, 'payment_intent.succeeded')) {
            return [
                'transaction_id' => $transactionId,
                'reference' => $payload['data']['object']['metadata']['invoice_id']
                    ?? $payload['data']['object']['invoice_id']
                    ?? (ctype_digit($transactionId) ? $transactionId : null),
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_status' => 'succeeded',
                'raw_response' => $payload,
            ];
        }

        return [
            'transaction_id' => $transactionId,
            'reference' => $payload['data']['object']['metadata']['invoice_id'] ?? null,
            'status' => 'pending',
            'paid_at' => null,
            'gateway_status' => $eventType ?: 'unknown',
            'raw_response' => $payload,
        ];
    }

    protected function apiVerifyWebhook(string $transactionId, array $payload): ?array
    {
        if (!$this->setupStripe()) {
            Log::warning('[Stripe] Webhook ignorado — secret_key ausente.');
            return null;
        }

        try {
            $eventType = $payload['type'] ?? '';

            if (str_starts_with($eventType, 'checkout.session.')) {
                $session = Session::retrieve($transactionId);
                $paymentStatus = $session->payment_status ?? 'unpaid';
                $metadata = $session->metadata ?? [];

                return [
                    'transaction_id' => $session->id,
                    'reference' => $metadata['invoice_id'] ?? null,
                    'status' => $paymentStatus === 'paid' ? 'paid' : 'pending',
                    'paid_at' => $paymentStatus === 'paid' ? new \DateTime() : null,
                    'gateway_status' => $paymentStatus,
                    'raw_response' => $session->toArray(),
                ];
            }

            if (str_starts_with($eventType, 'payment_intent.')) {
                $intent = PaymentIntent::retrieve($transactionId);

                return [
                    'transaction_id' => $intent->id,
                    'reference' => $intent->metadata['invoice_id']
                        ?? $intent->metadata['reference']
                        ?? $payload['data']['object']['metadata']['invoice_id'] ?? null,
                    'status' => $intent->status === 'succeeded' ? 'paid' : 'pending',
                    'paid_at' => $intent->status === 'succeeded' ? new \DateTime() : null,
                    'gateway_status' => $intent->status,
                    'raw_response' => $intent->toArray(),
                ];
            }

            Log::warning('[Stripe] Tipo de evento não suportado', ['type' => $eventType]);
            return null;
        } catch (ApiErrorException $e) {
            Log::warning('[Stripe] Erro na API ao processar webhook', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('[Stripe] Erro inesperado ao processar webhook', ['error' => $e->getMessage()]);
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
            Log::info("[Stripe][SANDBOX] {$message}", is_array($context) ? $context : ['context' => $context]);
        }
    }
}
