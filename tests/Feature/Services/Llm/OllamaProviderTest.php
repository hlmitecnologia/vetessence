<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Services\Llm\OllamaProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class OllamaProviderTest extends ModuleTestCase
{
    protected OllamaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new OllamaProvider;
    }

    public function test_generate_success()
    {
        Http::fake([
            'localhost:11434/api/chat' => Http::response([
                'message' => ['content' => 'Diagnóstico: Cinomose canina'],
                'model' => 'llama3',
                'prompt_eval_count' => 180,
                'eval_count' => 55,
            ], 200),
        ]);

        $config = LlmConfig::factory()->create([
            'provider' => 'ollama',
            'ollama_base_url' => 'http://localhost:11434',
            'ollama_model' => 'llama3',
        ]);
        $result = $this->provider->generate($config, 'Descreva os sintomas...');

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Cinomose canina', $result->content);
        $this->assertEquals('llama3', $result->model);
        $this->assertEquals(180, $result->inputTokens);
        $this->assertEquals(55, $result->outputTokens);
    }

    public function test_generate_with_custom_url()
    {
        Http::fake([
            'ollama.internal:11434/api/chat' => Http::response([
                'message' => ['content' => 'ok'],
                'model' => 'llama3',
            ], 200),
        ]);

        $config = LlmConfig::factory()->create([
            'provider' => 'ollama',
            'ollama_base_url' => 'http://ollama.internal:11434',
            'ollama_model' => 'llama3',
        ]);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertTrue($result->success);
    }

    public function test_generate_context_length_error()
    {
        Http::fake([
            'localhost:11434/api/chat' => Http::response([
                'error' => 'input too long: exceeds maximum context length',
            ], 400),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'ollama']);
        $result = $this->provider->generate($config, 'prompt longo...');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('too long', $result->errorMessage);
    }

    public function test_generate_connection_error()
    {
        Http::fake([
            'localhost:11434/api/chat' => Http::response(null, 503),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'ollama']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
    }
}
