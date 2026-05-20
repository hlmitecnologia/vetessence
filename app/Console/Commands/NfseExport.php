<?php

namespace App\Console\Commands;

use App\Models\NfseInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class NfseExport extends Command
{
    protected $signature = 'nfse:export {--from= : Data inicial (Y-m-d)} {--to= : Data final (Y-m-d)} {--branch= : ID da unidade}';
    protected $description = 'Export NFSe XMLs as ZIP for accounting';

    public function handle(): int
    {
        $query = NfseInvoice::where('status', 'issued')->whereNotNull('nfse_url_xml');

        if ($from = $this->option('from')) {
            $query->whereDate('issuance_date', '>=', $from);
        }

        if ($to = $this->option('to')) {
            $query->whereDate('issuance_date', '<=', $to);
        }

        if ($branchId = $this->option('branch')) {
            $query->where('branch_id', $branchId);
        }

        $nfseInvoices = $query->get();

        if ($nfseInvoices->isEmpty()) {
            $this->warn('Nenhuma NFSe encontrada para exportar.');
            return Command::SUCCESS;
        }

        $exportDir = 'nfse/exports';
        Storage::makeDirectory($exportDir);

        $zipPath = storage_path("app/{$exportDir}/nfse-export-" . now()->format('YmdHis') . '.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            $this->error('Falha ao criar arquivo ZIP.');
            return Command::FAILURE;
        }

        $downloaded = 0;

        foreach ($nfseInvoices as $nfse) {
            try {
                $response = Http::timeout(30)->get($nfse->nfse_url_xml);

                if ($response->successful()) {
                    $filename = "nfse-{$nfse->nfse_number}-{$nfse->branch_id}.xml";
                    $zip->addFromString($filename, $response->body());
                    $downloaded++;
                }
            } catch (\Exception $e) {
                $this->warn("Falha ao baixar XML #{$nfse->nfse_number}: {$e->getMessage()}");
            }
        }

        $zip->close();

        $this->info("Exportadas {$downloaded} NFSe(s) para: {$zipPath}");
        return Command::SUCCESS;
    }
}
