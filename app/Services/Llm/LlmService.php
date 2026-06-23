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

        $result = $this->resolveProvider($config)->generate($config, $prompt);

        if (!$result->success && $this->isTokenLimitError($result)) {
            return LlmResult::error(
                'O limite de tokens do modelo foi excedido porque o histórico do paciente contém muitos dados para a configuração atual. '
                . 'Para resolver, tente: (1) usar um modelo com janela de contexto maior '
                . '(ex: gpt-4o, claude-3-sonnet, gemini-1.5-flash), '
                . '(2) aumentar o limite de tokens máximo em Configurações > IA Diagnóstica, '
                . 'ou (3) reduzir o histórico de atendimentos. '
                . 'Nenhuma sugestão de diagnóstico será exibida até que o problema seja resolvido.'
            );
        }

        return $result;
    }

    protected function isTokenLimitError(LlmResult $result): bool
    {
        if ($result->success) {
            return false;
        }

        $msg = mb_strtolower($result->errorMessage ?? '');

        $patterns = [
            'context_length',
            'maximum context',
            'max tokens',
            'max_tokens',
            'too many tokens',
            'prompt is too long',
            'token limit',
            'context window',
            'token count',
            'input too long',
            'exceeds maximum',
            'too large',
            'reduce the length',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($msg, $pattern)) {
                return true;
            }
        }

        return false;
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
Você é um assistente de inteligência artificial especializado em Medicina Veterinária. Seu papel é auxiliar médicos veterinários na triagem e formulação de diagnósticos diferenciais.

**Regras Estritas:**

1. Baseie suas respostas na literatura científica veterinária atualizada (foco em caninos, felinos, equinos e ruminantes).
2. Diante dos sintomas fornecidos, crie uma lista estruturada de Diagnósticos Diferenciais do mais provável para o menos provável.
3. Sugira exames complementares específicos (Hematologia, Bioquímica, Ultrassom, PCR, etc.) para confirmar ou descartar as hipóteses.
4. Nunca forneça dosagens exatas de medicamentos, focando apenas nos princípios ativos indicados para a patologia.
5. Adicione sempre um aviso de que sua análise é informativa e não substitui a avaliação clínica presencial.

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

Com base nestas informações, siga as regras estritas acima e forneça sua análise.

**Importante:** Sua análise é informativa e não substitui a avaliação clínica presencial.
PROMPT;
    }
}
