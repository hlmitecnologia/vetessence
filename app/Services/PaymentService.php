<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\PaymentGatewayProviderFactory;
use InvalidArgumentException;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayProviderFactory $factory,
    ) {}

    public function charge(Invoice $invoice, string $channel = 'pdv'): array
    {
        $gateway = $this->getActiveGatewayForChannel($channel);

        if (!$gateway) {
            return [
                'success' => false,
                'message' => "Nenhum gateway ativo para o canal '{$channel}'.",
                'transaction_id' => null,
                'status' => 'failed',
                'redirect_url' => null,
                'raw_response' => [],
            ];
        }

        $provider = $this->factory->make($gateway);
        $result = $provider->charge($invoice);

        if ($result['success'] && $result['transaction_id']) {
            $invoice->update([
                'gateway_id' => $gateway->id,
                'gateway_transaction_id' => $result['transaction_id'],
                'gateway_status' => $result['status'],
            ]);
        }

        $result['gateway_name'] = $gateway->name;
        $result['gateway_provider'] = $gateway->provider;
        $result['is_sandbox'] = $gateway->is_sandbox;

        return $result;
    }

    public function checkout(Invoice $invoice): array
    {
        $gateway = $this->getActiveGatewayForChannel('portal');

        if (!$gateway) {
            $qrcode = $invoice->generatePixCode();
            return [
                'success' => true,
                'message' => 'QR Code PIX gerado.',
                'transaction_id' => 'PIX-' . $invoice->invoice_number,
                'status' => 'pending',
                'redirect_url' => null,
                'raw_response' => ['payload' => $qrcode['payload'], 'qrcode_base64' => $qrcode['qrcode_base64'] ?? null],
            ];
        }

        $provider = $this->factory->make($gateway);
        $result = $provider->checkout($invoice);

        if ($result['success'] && $result['transaction_id']) {
            $invoice->update([
                'gateway_id' => $gateway->id,
                'gateway_transaction_id' => $result['transaction_id'],
                'gateway_status' => $result['status'],
            ]);
        }

        $result['gateway_name'] = $gateway->name;
        $result['gateway_provider'] = $gateway->provider;
        $result['is_sandbox'] = $gateway->is_sandbox;

        return $result;
    }

    public function processWebhook(PaymentGateway $gateway, array $payload): array
    {
        $provider = $this->factory->make($gateway);
        $data = $provider->verifyWebhook($payload, $gateway);

        if (!$data || !isset($data['transaction_id'])) {
            return ['success' => false, 'message' => 'Webhook inválido ou não processado.'];
        }

        $invoice = Invoice::where('gateway_transaction_id', $data['transaction_id'])->first();

        if (!$invoice) {
            $invoiceId = $data['reference'] ?? null;

            if ($gateway->is_sandbox && !$invoiceId && ctype_digit($data['transaction_id'])) {
                $invoiceId = $data['transaction_id'];
            }

            if ($invoiceId) {
                $invoice = Invoice::where('id', $invoiceId)
                    ->where('gateway_id', $gateway->id)
                    ->first();
            }

            if (!$invoice) {
                return ['success' => false, 'message' => 'Nenhuma fatura encontrada para esta transação.'];
            }
        }

        if ($data['status'] === 'paid' && $invoice->status !== 'paid') {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $data['paid_at'] ?: now(),
                'gateway_status' => $data['gateway_status'],
                'gateway_paid_at' => $data['paid_at'] ?: now(),
                'payment_method' => $data['payment_method'] ?? 'cartao_credito',
            ]);
            \App\Events\InvoicePaid::dispatch($invoice);
        } elseif ($data['status'] === 'cancelled' && $invoice->status !== 'cancelled') {
            $invoice->update([
                'status' => 'cancelled',
                'gateway_status' => 'cancelled',
            ]);
        } elseif ($data['status'] !== $invoice->gateway_status) {
            $invoice->update([
                'gateway_status' => $data['gateway_status'],
            ]);
        }

        return [
            'success' => true,
            'message' => 'Webhook processado com sucesso.',
            'invoice_id' => $invoice->id,
            'new_status' => $invoice->status,
        ];
    }

    public function getActiveGatewayForChannel(string $channel): ?PaymentGateway
    {
        $branchId = $this->getCurrentBranchId();

        $query = PaymentGateway::withoutBranch()->active()
            ->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id');
                if ($branchId) {
                    $q->orWhere('branch_id', $branchId);
                }
            })
            ->where(function ($q) use ($channel) {
                $q->where('channel', $channel);
                if ($channel === 'portal') {
                    $q->orWhere('channel', 'both');
                }
                if ($channel === 'pdv') {
                    $q->orWhere('channel', 'both');
                }
            });

        return $query->first();
    }

    protected function getCurrentBranchId(): ?int
    {
        return \App\Services\BranchContext::hasBranch()
            ? \App\Services\BranchContext::get()
            : null;
    }
}
