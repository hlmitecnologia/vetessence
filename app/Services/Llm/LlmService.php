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

        return <<<PROMPT
Com base nos dados abaixo, sugira um diagnóstico principal e diagnóstico diferencial para este paciente veterinário.

**Dados do Paciente:**
- Espécie: {$species}
- Raça: {$breed}
- Idade: {$age}
- Sexo: {$gender}

**Sinais Vitais:**
{$vitalText}

**Queixa Principal:**
{$record->chief_complaint}

**Anamnese:**
{$record->anamnesis}

**Exame Físico:**
{$record->physical_exam}

Com base nestas informações, forneça:
1. Diagnóstico(s) suspeito(s)
2. Diagnóstico(s) diferencial(is)
3. Breve justificativa clínica

Responda de forma objetiva e profissional.
PROMPT;
    }
}
