<?php

namespace App\Services\Nfe;

use App\Models\CommunicationQueue;
use App\Models\Invoice;
use App\Models\NfeConfig;
use App\Models\NfeInvoice;

class NfeService
{
    public function __construct(
        protected ?NfeProvider $provider = null,
    ) {}

    public function emitir(Invoice $invoice): NfeResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return NfeResult::error('NF-e não configurada para o sistema.');
        }

        if (!$invoice->branch || !$invoice->branch->cnpj) {
            return NfeResult::error('Dados fiscais da unidade incompletos. Configure o CNPJ no cadastro da unidade.');
        }

        if ($invoice->nfe_status === 'issued') {
            return NfeResult::error('Esta fatura já possui uma NF-e emitida.');
        }

        $provider = $this->resolveProvider($config);
        $result = $provider->emitir($config, $invoice);

        $nfeInvoice = NfeInvoice::firstOrNew(['invoice_id' => $invoice->id]);
        $nfeInvoice->branch_id = $invoice->branch_id;

        if ($result->success) {
            $nfeInvoice->fill([
                'nfe_number' => $result->nfeNumber,
                'nfe_key' => $result->nfeKey,
                'nfe_url_xml' => $result->xmlUrl,
                'nfe_url_pdf' => $result->pdfUrl,
                'danfe_url' => $result->danfeUrl,
                'status' => 'issued',
                'issuance_date' => now(),
                'provider_response' => $result->rawResponse,
                'error_message' => null,
            ])->save();

            $invoice->update([
                'nfe_status' => 'issued',
                'nfe_invoice_id' => $nfeInvoice->id,
            ]);

            $this->notifyTutor($invoice, $nfeInvoice);
        } else {
            $nfeInvoice->fill([
                'status' => 'failed',
                'provider_response' => $result->rawResponse,
                'error_message' => $result->errorMessage,
            ])->save();
        }

        return $result;
    }

    public function emitirTransferencia(
        \App\Models\Product $product,
        \App\Models\Branch $fromBranch,
        \App\Models\Branch $toBranch,
        float $quantity,
        \App\Models\User $user,
    ): NfeResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return NfeResult::error('NF-e não configurada para o sistema.');
        }

        $provider = $this->resolveProvider($config);
        $result = $provider->emitirTransferencia($config, [
            'product' => $product,
            'from_branch' => $fromBranch,
            'to_branch' => $toBranch,
            'quantity' => $quantity,
        ]);

        if ($result->success) {
            \App\Models\NfeTransfer::create([
                'branch_id' => $fromBranch->id,
                'from_branch_id' => $fromBranch->id,
                'to_branch_id' => $toBranch->id,
                'product_id' => $product->id,
                'user_id' => $user->id,
                'nfe_number' => $result->nfeNumber,
                'nfe_key' => $result->nfeKey,
                'status' => 'issued',
                'issuance_date' => now(),
                'nfe_url_xml' => $result->xmlUrl,
                'nfe_url_pdf' => $result->pdfUrl,
                'danfe_url' => $result->danfeUrl,
                'provider_response' => $result->rawResponse,
            ]);
        }

        return $result;
    }

    public function cancelar(Invoice $invoice, string $motivo): NfeResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return NfeResult::error('NF-e não configurada para o sistema.');
        }

        $nfeInvoice = $invoice->nfeInvoice;

        if (!$nfeInvoice || $nfeInvoice->status !== 'issued') {
            return NfeResult::error('NF-e não encontrada ou já cancelada.');
        }

        $provider = $this->resolveProvider($config);
        $result = $provider->cancelar($config, $nfeInvoice->nfe_number, $motivo, $nfeInvoice->nfe_key);

        if ($result->success) {
            $nfeInvoice->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $invoice->update(['nfe_status' => 'cancelled']);
        }

        return $result;
    }

    public function getConfig(): ?NfeConfig
    {
        return NfeConfig::where('is_active', true)->first();
    }

    protected function resolveProvider(NfeConfig $config): NfeProvider
    {
        if ($this->provider) {
            return $this->provider;
        }

        return match ($config->provider) {
            'focusnfe' => app(FocusNfeProvider::class),
            'nfeio' => app(NfeIoProvider::class),
            'webmania' => app(WebmaniaProvider::class),
            default => throw new \InvalidArgumentException("Provedor NF-e desconhecido: {$config->provider}"),
        };
    }

    protected function notifyTutor(Invoice $invoice, NfeInvoice $nfeInvoice): void
    {
        if (!$invoice->tutor || !$invoice->tutor->email) {
            return;
        }

        CommunicationQueue::create([
            'branch_id' => $invoice->branch_id,
            'tutor_id' => $invoice->tutor_id,
            'pet_id' => $invoice->pet_id,
            'channel' => 'email',
            'destination' => $invoice->tutor->email,
            'message_content' => "NF-e emitida: {$nfeInvoice->nfe_number} - Fatura {$invoice->invoice_number}. Acesse o sistema para visualizar o XML e DANFE.",
            'status' => 'pending',
            'scheduled_at' => now(),
        ]);
    }
}
