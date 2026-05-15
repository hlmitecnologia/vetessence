<?php

namespace App\Console\Commands;

use App\Models\Tutor;
use Illuminate\Console\Command;

class AnonimizarDadosLGPD extends Command
{
    protected $signature = 'lgpd:anonymize {tutor_id}';
    protected $description = 'Anonymize all personal data for a tutor (LGPD Article 16)';

    public function handle()
    {
        $tutor = Tutor::find($this->argument('tutor_id'));

        if (!$tutor) {
            $this->error('Tutor não encontrado.');
            return 1;
        }

        $tutor->update([
            'name' => '[ANONYMIZED]',
            'cpf' => null,
            'rg' => null,
            'email' => "anonimo{$tutor->id}@vetessence.anon",
            'phone' => null,
            'phone_secondary' => null,
            'zipcode' => null,
            'address' => null,
            'number' => null,
            'complement' => null,
            'neighborhood' => null,
            'city' => null,
            'state' => null,
        ]);

        $tutor->revokeConsent('lgpd_data_processing');

        $this->info("Dados do tutor {$tutor->id} anonimizados com sucesso.");
        return 0;
    }
}
