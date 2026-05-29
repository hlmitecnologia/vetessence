<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Services\Llm\AnthropicProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class AnthropicProviderTest extends ModuleTestCase
{
    protected AnthropicProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new AnthropicProvider;
    }

    public function test_generate_success()
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [['text' => 'Diagnóstico: Parvovirose']],
                'model' => 'claude-3-haiku-20240307',
                'usage' => ['input_tokens' => 200, 'output_tokens' => 60],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'anthropic']);
        $result = $this->provider->generate($config, 'Descreva os sintomas...');

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Parvovirose', $result->content);
        $this->assertEquals('claude-3-haiku-20240307', $result->model);
        $this->assertEquals(200, $result->inputTokens);
        $this->assertEquals(60, $result->outputTokens);
    }

    public function test_generate_api_error()
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'error' => ['message' => 'Invalid API key'],
            ], 401),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'anthropic']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertEquals('Invalid API key', $result->errorMessage);
    }

    public function test_generate_context_length_error()
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'error' => ['message' => 'prompt is too long: your prompt has 200001 tokens'],
            ], 400),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'anthropic']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('too long', $result->errorMessage);
    }
}
