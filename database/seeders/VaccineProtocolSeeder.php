<?php

namespace Database\Seeders;

use App\Models\VaccineProtocol;
use Illuminate\Database\Seeder;

class VaccineProtocolSeeder extends Seeder
{
    public function run()
    {
        $protocols = [
            // === CANINE ===
            ['species' => 'canine', 'vaccine_name' => 'V8 (Polivalente)', 'age_start_weeks' => 6, 'age_end_weeks' => 8, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => null, 'is_core' => true, 'notes' => '1ª dose da série filhote. Protege contra cinomose, parvovirose, adenovírus, parainfluenza e coronavírus.'],
            ['species' => 'canine', 'vaccine_name' => 'V8 (Polivalente)', 'age_start_weeks' => 9, 'age_end_weeks' => 11, 'is_initial' => true, 'dose_number' => 2, 'booster_interval_months' => null, 'is_core' => true, 'notes' => '2ª dose. Intervalo mínimo de 21 dias após a 1ª.'],
            ['species' => 'canine', 'vaccine_name' => 'V8 (Polivalente)', 'age_start_weeks' => 12, 'age_end_weeks' => 16, 'is_initial' => true, 'dose_number' => 3, 'booster_interval_months' => null, 'is_core' => true, 'notes' => '3ª dose. Intervalo mínimo de 21 dias após a 2ª.'],
            ['species' => 'canine', 'vaccine_name' => 'V8 (Polivalente)', 'age_start_weeks' => null, 'age_end_weeks' => null, 'is_initial' => false, 'dose_number' => null, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Reforço anual obrigatório.'],
            ['species' => 'canine', 'vaccine_name' => 'Antirrábica', 'age_start_weeks' => 12, 'age_end_weeks' => 16, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Dose única inicial a partir de 12 semanas. Reforço anual obrigatório por lei.'],
            ['species' => 'canine', 'vaccine_name' => 'V10 (Polivalente)', 'age_start_weeks' => 6, 'age_end_weeks' => 8, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => null, 'is_core' => false, 'notes' => 'Similar à V8, com proteção adicional para leptospirose.'],
            ['species' => 'canine', 'vaccine_name' => 'Giárdia', 'age_start_weeks' => 8, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => false, 'notes' => 'Recomendada para cães com acesso a áreas externas ou ambientes coletivos.'],
            ['species' => 'canine', 'vaccine_name' => 'Tosse dos Canis (Bordetella)', 'age_start_weeks' => 8, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 6, 'is_core' => false, 'notes' => 'Recomendada para cães que frequentam pet shops, hotéis ou creches. Reforço semestral.'],
            ['species' => 'canine', 'vaccine_name' => 'Leishmaniose', 'age_start_weeks' => 16, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => false, 'notes' => 'Três doses com intervalo de 21 dias. Reforço anual. Recomendada em áreas endêmicas.'],

            // === FELINE ===
            ['species' => 'feline', 'vaccine_name' => 'V3 (Panleucopenia + Rinotraqueíte + Calicivirose)', 'age_start_weeks' => 8, 'age_end_weeks' => 9, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => null, 'is_core' => true, 'notes' => '1ª dose da série filhote.'],
            ['species' => 'feline', 'vaccine_name' => 'V3 (Panleucopenia + Rinotraqueíte + Calicivirose)', 'age_start_weeks' => 12, 'age_end_weeks' => 13, 'is_initial' => true, 'dose_number' => 2, 'booster_interval_months' => null, 'is_core' => true, 'notes' => '2ª dose.'],
            ['species' => 'feline', 'vaccine_name' => 'V3 (Panleucopenia + Rinotraqueíte + Calicivirose)', 'age_start_weeks' => 16, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 3, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => '3ª dose (opcional em alguns protocolos). Reforço anual.'],
            ['species' => 'feline', 'vaccine_name' => 'Antirrábica', 'age_start_weeks' => 12, 'age_end_weeks' => 16, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Dose única inicial. Reforço anual obrigatório.'],
            ['species' => 'feline', 'vaccine_name' => 'V4 ou V5', 'age_start_weeks' => 8, 'age_end_weeks' => 9, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => null, 'is_core' => false, 'notes' => 'V4 adiciona clamidiose; V5 adiciona clamidiose + FeLV.'],
            ['species' => 'feline', 'vaccine_name' => 'FeLV (Leucemia Felina)', 'age_start_weeks' => 8, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => false, 'notes' => 'Duas doses com intervalo de 21-28 dias. Reforço anual. Testar FeLV antes de vacinar.'],
            ['species' => 'feline', 'vaccine_name' => 'FIV (Imunodeficiência Felina)', 'age_start_weeks' => 8, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => false, 'notes' => 'Três doses. Testar FIV antes de vacinar.'],

            // === EQUINE ===
            ['species' => 'equine', 'vaccine_name' => 'Influenza Equina', 'age_start_weeks' => 24, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 6, 'is_core' => false, 'notes' => 'Série inicial: 3 doses. Reforço semestral.'],
            ['species' => 'equine', 'vaccine_name' => 'Tétano', 'age_start_weeks' => 24, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Reforço anual.'],
            ['species' => 'equine', 'vaccine_name' => 'Raiva (Equina)', 'age_start_weeks' => 24, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Obrigatória em algumas regiões. Reforço anual.'],
            ['species' => 'equine', 'vaccine_name' => 'Encefalomielite (Leste/Oeste/Venezuelana)', 'age_start_weeks' => 24, 'age_end_weeks' => null, 'is_initial' => true, 'dose_number' => 1, 'booster_interval_months' => 12, 'is_core' => true, 'notes' => 'Reforço anual antes da temporada de mosquitos.'],
        ];

        foreach ($protocols as $data) {
            VaccineProtocol::create($data);
        }

        $this->command->info('Seeded ' . count($protocols) . ' vaccine protocols.');
    }
}
