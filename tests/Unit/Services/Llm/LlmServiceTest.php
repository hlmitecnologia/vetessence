<?php

namespace Tests\Unit\Services\Llm;

use App\Models\LlmConfig;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Services\Llm\LlmProvider;
use App\Services\Llm\LlmResult;
use App\Services\Llm\LlmService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LlmServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        LlmConfig::whereNotNull('id')->delete();
    }
    private function makeResult(string $message): LlmResult
    {
        return LlmResult::error($message);
    }

    public static function tokenLimitProvider(): array
    {
        return [
            'context_length_exceeded' => ['context_length_exceeded'],
            'maximum context length' => ['This model maximum context length is 128000 tokens'],
            'max tokens exceeded' => ['max tokens exceeded'],
            'max_tokens error' => ['max_tokens limit reached'],
            'too many tokens' => ['too many tokens in the prompt'],
            'prompt is too long' => ['prompt is too long'],
            'token limit' => ['token limit reached'],
            'context window' => ['exceeds the context window'],
            'token count' => ['token count exceeds limit'],
            'input too long' => ['input too long for model'],
            'exceeds maximum' => ['exceeds maximum length'],
            'too large' => ['request too large'],
            'reduce the length' => ['reduce the length of the messages'],
        ];
    }

    /** @dataProvider tokenLimitProvider */
    public function test_is_token_limit_error_detects_all_patterns(string $errorMessage)
    {
        $service = new LlmService();
        $result = $this->makeResult($errorMessage);

        $reflection = new \ReflectionMethod($service, 'isTokenLimitError');

        $this->assertTrue($reflection->invoke($service, $result));
    }

    public function test_is_token_limit_error_returns_false_for_other_errors()
    {
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'isTokenLimitError');

        $errors = [
            'invalid_api_key',
            'Insufficient balance',
            'Rate limit exceeded',
            'Server error',
            'Connection timeout',
            'Model not found',
            '',
        ];

        foreach ($errors as $error) {
            $result = $this->makeResult($error);
            $this->assertFalse($reflection->invoke($service, $result), "Failed for: $error");
        }
    }

    public function test_is_token_limit_error_returns_false_for_success()
    {
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'isTokenLimitError');
        $result = LlmResult::success(content: 'ok');

        $this->assertFalse($reflection->invoke($service, $result));
    }

    public function test_resolve_provider_openai()
    {
        $config = LlmConfig::factory()->make(['provider' => 'openai']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $provider = $reflection->invoke($service, $config);

        $this->assertInstanceOf(LlmProvider::class, $provider);
        $this->assertInstanceOf(\App\Services\Llm\OpenAiProvider::class, $provider);
    }

    public function test_resolve_provider_anthropic()
    {
        $config = LlmConfig::factory()->make(['provider' => 'anthropic']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $provider = $reflection->invoke($service, $config);

        $this->assertInstanceOf(\App\Services\Llm\AnthropicProvider::class, $provider);
    }

    public function test_resolve_provider_gemini()
    {
        $config = LlmConfig::factory()->make(['provider' => 'gemini']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $provider = $reflection->invoke($service, $config);

        $this->assertInstanceOf(\App\Services\Llm\GeminiProvider::class, $provider);
    }

    public function test_resolve_provider_grok()
    {
        $config = LlmConfig::factory()->make(['provider' => 'grok']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $provider = $reflection->invoke($service, $config);

        $this->assertInstanceOf(\App\Services\Llm\GrokProvider::class, $provider);
    }

    public function test_resolve_provider_ollama()
    {
        $config = LlmConfig::factory()->make(['provider' => 'ollama']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $provider = $reflection->invoke($service, $config);

        $this->assertInstanceOf(\App\Services\Llm\OllamaProvider::class, $provider);
    }

    public function test_resolve_provider_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $config = LlmConfig::factory()->make(['provider' => 'invalid_provider']);
        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');

        $reflection->invoke($service, $config);
    }

    public function test_build_prompt_contains_expected_sections()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'chief_complaint' => 'Vômito e diarreia',
            'anamnesis' => 'Há 2 dias',
            'physical_exam' => 'Mucosas hipocoradas',
            'vital_signs' => ['temperature' => '39.5', 'heart_rate' => '120'],
            'treatment' => 'Fluidoterapia',
        ]);

        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'buildPrompt');
        $prompt = $reflection->invoke($service, $record, $pet);

        $this->assertStringContainsString('assiste', $prompt);
        $this->assertStringContainsString('Dados do Paciente', $prompt);
        $this->assertStringContainsString('Sinais Vitais', $prompt);
        $this->assertStringContainsString('Queixa Principal', $prompt);
        $this->assertStringContainsString('Vômito e diarreia', $prompt);
        $this->assertStringContainsString('Anamnese', $prompt);
        $this->assertStringContainsString('Exame Físico', $prompt);
        $this->assertStringContainsString('Tratamento Atual', $prompt);
        $this->assertStringContainsString('Fluidoterapia', $prompt);
        $this->assertStringContainsString('Medicações em Andamento', $prompt);
        $this->assertStringContainsString('Diagnóstico(s) suspeito(s)', $prompt);
        $this->assertStringContainsString('Diagnóstico(s) diferencial(is)', $prompt);
        $this->assertStringContainsString('sugira ajustes', $prompt);
    }

    public function test_build_prompt_includes_vital_signs()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'vital_signs' => [
                'temperature' => '38.5',
                'heart_rate' => '110',
                'respiratory_rate' => '30',
                'weight' => '15.5',
                'mucosa' => 'Normocoradas',
                'hydration' => 'Normal',
                'lymph_nodes' => 'Normais',
            ],
        ]);

        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'buildPrompt');
        $prompt = $reflection->invoke($service, $record, $pet);

        $this->assertStringContainsString('38.5', $prompt);
        $this->assertStringContainsString('110', $prompt);
        $this->assertStringContainsString('Normocoradas', $prompt);
    }

    public function test_build_prompt_without_vital_signs()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create([
            'pet_id' => $pet->id,
            'vital_signs' => [],
        ]);

        $service = new LlmService();
        $reflection = new \ReflectionMethod($service, 'buildPrompt');
        $prompt = $reflection->invoke($service, $record, $pet);

        $this->assertStringNotContainsString('Temperatura:', $prompt);
    }

    public function test_suggest_diagnosis_wraps_token_limit_error()
    {
        $config = LlmConfig::factory()->create(['is_active' => true, 'provider' => 'openai']);
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);

        $mock = $this->createMock(LlmProvider::class);
        $mock->method('generate')->willReturn(
            LlmResult::error('context_length_exceeded: you can not exceed 128000 tokens')
        );

        $service = new LlmService($mock);
        $result = $service->suggestDiagnosis($record);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('limite de tokens', mb_strtolower($result->errorMessage));
        $this->assertStringContainsString('Configurações > IA Diagnóstica', $result->errorMessage);
        $this->assertStringContainsString('Nenhuma sugestão', $result->errorMessage);
    }

    public function test_suggest_diagnosis_without_config()
    {
        LlmConfig::whereNotNull('id')->delete();

        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);

        $service = new LlmService();
        $result = $service->suggestDiagnosis($record);

        $this->assertFalse($result->success);
        $this->assertEquals('IA diagnóstica não configurada.', $result->errorMessage);
    }

    public function test_suggest_diagnosis_returns_error_when_no_active_config()
    {
        $pet = Pet::factory()->create();
        $record = MedicalRecord::factory()->create(['pet_id' => $pet->id]);

        $service = new LlmService();
        $result = $service->suggestDiagnosis($record);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('não configurada', $result->errorMessage);
    }

    public function test_get_config_returns_null_when_none_active()
    {
        LlmConfig::whereNotNull('id')->delete();
        $service = new LlmService();
        $this->assertNull($service->getConfig());
    }

    public function test_get_config_returns_active_config()
    {
        $config = LlmConfig::factory()->create(['is_active' => true]);
        $service = new LlmService();
        $this->assertNotNull($service->getConfig());
        $this->assertTrue($service->getConfig()->is_active);
    }
}
