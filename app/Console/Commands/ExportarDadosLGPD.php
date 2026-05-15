<?php

namespace App\Console\Commands;

use App\Models\Tutor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportarDadosLGPD extends Command
{
    protected $signature = 'lgpd:export {tutor_id}';
    protected $description = 'Export all personal data for a tutor (LGPD Article 9)';

    public function handle()
    {
        $tutor = Tutor::with(['pets', 'invoices', 'consentLogs'])->find($this->argument('tutor_id'));

        if (!$tutor) {
            $this->error('Tutor não encontrado.');
            return 1;
        }

        $data = [
            'exported_at' => now()->toIso8601String(),
            'tutor' => $tutor->toArray(),
        ];

        $filename = "lgpd-export-{$tutor->id}-" . now()->format('YmdHis') . '.json';
        Storage::put("exports/{$filename}", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Dados exportados para: storage/app/exports/{$filename}");
        return 0;
    }
}
