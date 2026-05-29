<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Services\Llm\GrokProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class GrokProviderTest extends ModuleTestCase
{
    protected GrokProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new GrokProvider;
    }

    public function test_generate_success()
    {
        Http::fake([
            'api.x.ai/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Diagnóstico: Leptospirose']]],
                'model' => 'grok-1',
                'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 40],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'grok']);
        $result = $this->provider->generate($config, 'Descreva os sintomas...');

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Leptospirose', $result->content);
        $this->assertEquals('grok-1', $result->model);
        $this->assertEquals(100, $result->inputTokens);
        $this->assertEquals(40, $result->outputTokens);
    }

    public function test_generate_api_error()
    {
        Http::fake([
            'api.x.ai/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Insufficient balance'],
            ], 402),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'grok']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertEquals('Insufficient balance', $result->errorMessage);
    }

    public function test_generate_context_length_error()
    {
        Http::fake([
            'api.x.ai/v1/chat/completions' => Http::response([
                'error' => ['message' => 'context_length_exceeded'],
            ], 400),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'grok']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('context_length', $result->errorMessage);
    }
}
