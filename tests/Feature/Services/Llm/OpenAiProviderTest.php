<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Services\Llm\OpenAiProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class OpenAiProviderTest extends ModuleTestCase
{
    protected OpenAiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new OpenAiProvider;
    }

    public function test_generate_success()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Diagnóstico: Cinomose']]],
                'model' => 'gpt-4o-mini',
                'usage' => ['prompt_tokens' => 150, 'completion_tokens' => 50],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'openai']);
        $result = $this->provider->generate($config, 'Descreva os sintomas...');

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Cinomose', $result->content);
        $this->assertEquals('gpt-4o-mini', $result->model);
        $this->assertEquals(150, $result->inputTokens);
        $this->assertEquals(50, $result->outputTokens);
    }

    public function test_generate_api_error()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Invalid API key'],
            ], 401),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'openai']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertEquals('Invalid API key', $result->errorMessage);
    }

    public function test_generate_context_length_error()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'error' => ['message' => 'context_length_exceeded: This model maximum context length is 128000 tokens'],
            ], 400),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'openai']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('context_length', $result->errorMessage);
    }

    public function test_generate_timeout()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response(null, 408),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'openai']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
    }
}
