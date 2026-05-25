<?php

namespace App\Services\Nfse;

use App\Models\CommunicationQueue;
use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Models\NfseInvoice;

class NfseService
{
    public function __construct(
        protected ?NfseProvider $provider = null,
    ) {}

    public function emitir(Invoice $invoice): NfseResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return NfseResult::error('NFS-e não configurada para o sistema.');
        }

        if (!$invoice->branch || !$invoice->branch->municipio_ibge) {
            return NfseResult::error('Dados fiscais da unidade incompletos. Configure o código IBGE no cadastro da unidade.');
        }

        if ($invoice->nfse_status !== 'none') {
            return NfseResult::error('Esta fatura já possui uma NFS-e emitida ou em processo.');
        }

        $provider = $this->resolveProvider($config);
        $result = $provider->emitir($config, $invoice);

        if ($result->success) {
            $nfseInvoice = NfseInvoice::create([
                'branch_id' => $invoice->branch_id,
                'invoice_id' => $invoice->id,
                'nfse_number' => $result->nfseNumber,
                'nfse_code' => $result->nfseCode,
                'nfse_url_xml' => $result->xmlUrl,
                'nfse_url_pdf' => $result->pdfUrl,
                'rps_number' => $result->rpsNumber,
                'status' => 'issued',
                'issuance_date' => now(),
                'verification_code' => $result->verificationCode,
                'provider_response' => json_encode($result->rawResponse),
            ]);

            $invoice->update([
                'nfse_status' => 'issued',
                'nfse_invoice_id' => $nfseInvoice->id,
            ]);

            $this->notifyTutor($invoice, $nfseInvoice);
        }

        return $result;
    }

    public function cancelar(Invoice $invoice, string $motivo): NfseResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return NfseResult::error('NFS-e não configurada para o sistema.');
        }

        $nfseInvoice = $invoice->nfseInvoice;

        if (!$nfseInvoice || $nfseInvoice->status !== 'issued') {
            return NfseResult::error('NFS-e não encontrada ou já cancelada.');
        }

        if ($nfseInvoice->issuance_date && $nfseInvoice->issuance_date->diffInHours(now()) > 24) {
            return NfseResult::error('Prazo de cancelamento de 24h excedido. Solicite o cancelamento junto à prefeitura.');
        }

        $provider = $this->resolveProvider($config);
        $result = $provider->cancelar($config, $nfseInvoice->nfse_number, $motivo);

        if ($result->success) {
            $nfseInvoice->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $invoice->update(['nfse_status' => 'cancelled']);
        }

        return $result;
    }

    public function getConfig(): ?NfseConfig
    {
        return NfseConfig::where('is_active', true)->first();
    }

    protected function resolveProvider(NfseConfig $config): NfseProvider
    {
        if ($this->provider) {
            return $this->provider;
        }

        return match ($config->provider) {
            'webmania' => app(WebmaniaProvider::class),
            'focusnfe' => app(FocusNfeProvider::class),
            'ginfes' => app(GinfesProvider::class),
            default => throw new \InvalidArgumentException("Provedor NFS-e desconhecido: {$config->provider}"),
        };
    }

    protected function notifyTutor(Invoice $invoice, NfseInvoice $nfseInvoice): void
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
            'message_content' => "NFSe emitida: {$nfseInvoice->nfse_number} - Fatura {$invoice->invoice_number}. Acesse o sistema para visualizar o XML e PDF.",
            'status' => 'pending',
            'scheduled_at' => now(),
        ]);
    }
}
