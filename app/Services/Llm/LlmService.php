<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;
use App\Models\MedicalRecord;

class LlmService
{
    public function __construct(
        protected ?LlmProvider $provider = null,
    ) {}

    public function suggestDiagnosis(MedicalRecord $record): LlmResult
    {
        $config = $this->getConfig();

        if (!$config) {
            return LlmResult::error('IA diagnóstica não configurada.');
        }

        if (!$config->is_active) {
            return LlmResult::error('IA diagnóstica está desativada.');
        }

        $pet = $record->pet ?? \App\Models\Pet::find($record->pet_id);

        $prompt = $this->buildPrompt($record, $pet);

        $provider = $this->resolveProvider($config);

        return $provider->generate($config, $prompt);
    }

    public function getConfig(): ?LlmConfig
    {
        return LlmConfig::where('is_active', true)->first();
    }

    protected function resolveProvider(LlmConfig $config): LlmProvider
    {
        if ($this->provider) {
            return $this->provider;
        }

        return match ($config->provider) {
            'openai' => app(OpenAiProvider::class),
            'anthropic' => app(AnthropicProvider::class),
            'gemini' => app(GeminiProvider::class),
            'grok' => app(GrokProvider::class),
            'ollama' => app(OllamaProvider::class),
            default => throw new \InvalidArgumentException("Provedor LLM desconhecido: {$config->provider}"),
        };
    }

    protected function buildPrompt(MedicalRecord $record, ?\App\Models\Pet $pet = null): string
    {
        $pet = $pet ?? $record->pet ?? \App\Models\Pet::find($record->pet_id);

        $species = $pet->species ?? 'não informada';
        $breed = $pet->breed ?? 'não informada';
        $age = $pet->age ?? 'não informada';
        $gender = $pet->gender ?? 'não informado';

        $vitalSigns = $record->vital_signs ?? [];
        $vitalText = '';
        if (!empty($vitalSigns)) {
            $parts = [];
            if (!empty($vitalSigns['temperature'])) $parts[] = "Temperatura: {$vitalSigns['temperature']}";
            if (!empty($vitalSigns['heart_rate'])) $parts[] = "FC: {$vitalSigns['heart_rate']}";
            if (!empty($vitalSigns['respiratory_rate'])) $parts[] = "FR: {$vitalSigns['respiratory_rate']}";
            if (!empty($vitalSigns['weight'])) $parts[] = "Peso: {$vitalSigns['weight']}";
            if (!empty($vitalSigns['mucosa'])) $parts[] = "Mucosas: {$vitalSigns['mucosa']}";
            if (!empty($vitalSigns['hydration'])) $parts[] = "Hidratação: {$vitalSigns['hydration']}";
            if (!empty($vitalSigns['lymph_nodes'])) $parts[] = "Linfonodos: {$vitalSigns['lymph_nodes']}";
            $vitalText = implode("\n", $parts);
        }

        $historyText = '';
        if ($pet && $pet->relationLoaded('medicalRecords') || $pet->medicalRecords()->exists()) {
            $pastRecords = $pet->medicalRecords()
                ->when($record->exists, fn($q) => $q->where('id', '!=', $record->id))
                ->latest('date')
                ->limit(5)
                ->get(['id', 'date', 'diagnosis', 'treatment']);

            if ($pastRecords->isNotEmpty()) {
                $lines = $pastRecords->map(fn($r) =>
                    '- ' . ($r->date?->format('d/m/Y') ?? '??') . ' | Diagnóstico: ' . ($r->diagnosis ?? '-') . ' | Tratamento: ' . ($r->treatment ?? '-')
                )->toArray();
                $historyText = implode("\n", $lines);
            }
        }

        $vaccinationText = '';
        if ($pet && $pet->relationLoaded('vaccinations') || $pet->vaccinations()->exists()) {
            $vaccinations = $pet->vaccinations()
                ->latest('date')
                ->limit(5)
                ->get(['vaccine', 'date', 'next_date']);

            if ($vaccinations->isNotEmpty()) {
                $lines = $vaccinations->map(fn($v) =>
                    '- ' . ($v->vaccine ?? '?') . ' em ' . ($v->date?->format('d/m/Y') ?? '??') . ($v->next_date ? ' (próximo: ' . $v->next_date->format('d/m/Y') . ')' : '')
                )->toArray();
                $vaccinationText = implode("\n", $lines);
            }
        }

        $treatment = trim($record->treatment ?? '');
        $prescriptionText = '';
        if ($record->exists && $record->relationLoaded('prescriptions') || ($record->exists && $record->prescriptions()->exists())) {
            $prescriptions = $record->prescriptions()->get(['medication', 'dosage', 'unit', 'frequency', 'duration', 'route']);
            if ($prescriptions->isNotEmpty()) {
                $lines = $prescriptions->map(fn($p) =>
                    '- ' . ($p->medication ?? '?') . ($p->dosage ? ' ' . $p->dosage . ($p->unit ?? '') : '') . ($p->frequency ? ' ' . $p->frequency : '') . ($p->route ? ' ' . $p->route : '') . ($p->duration ? ' por ' . $p->duration : '')
                )->toArray();
                $prescriptionText = implode("\n", $lines);
            }
        } elseif (!$record->exists) {
            $prescriptionText = '(em definição pelo veterinário)';
        }

        return <<<PROMPT
Você é um veterinário especialista em diagnóstico animal. Com base nos dados abaixo, sugira um diagnóstico principal e diagnóstico diferencial para este paciente veterinário, e avalie o tratamento em andamento.

**Dados do Paciente:**
- Espécie: {$species}
- Raça: {$breed}
- Idade: {$age}
- Sexo: {$gender}

**Sinais Vitais:**
{$vitalText}

**Histórico de Atendimentos (últimos):**
{$historyText}

**Vacinações:**
{$vaccinationText}

**Queixa Principal:**
{$record->chief_complaint}

**Anamnese:**
{$record->anamnesis}

**Exame Físico:**
{$record->physical_exam}

**Tratamento Atual:**
{$treatment}

**Medicações em Andamento:**
{$prescriptionText}

Com base nestas informações, forneça:
1. Diagnóstico(s) suspeito(s)
2. Diagnóstico(s) diferencial(is)
3. Breve justificativa clínica
4. Se houver tratamento e medicações atuais, sugira ajustes ou confirme a adequação do tratamento em andamento

Responda de forma objetiva e profissional.
PROMPT;
    }
}
