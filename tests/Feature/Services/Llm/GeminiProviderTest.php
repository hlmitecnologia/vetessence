<?php

namespace Tests\Feature\Services\Llm;

use App\Models\LlmConfig;
use App\Services\Llm\GeminiProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class GeminiProviderTest extends ModuleTestCase
{
    protected GeminiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new GeminiProvider;
    }

    public function test_generate_success()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => ['parts' => [['text' => 'Diagnóstico: Erliquiose']]],
                        'finishReason' => 'STOP',
                    ],
                ],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'gemini']);
        $result = $this->provider->generate($config, 'Descreva os sintomas...');

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Erliquiose', $result->content);
    }

    public function test_generate_max_tokens_error()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => ['parts' => [['text' => 'Diagnóstico parcial...']]],
                        'finishReason' => 'MAX_TOKENS',
                    ],
                ],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'gemini']);
        $result = $this->provider->generate($config, 'prompt longo...');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('limite de tokens', mb_strtolower($result->errorMessage));
    }

    public function test_generate_empty_candidates()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [],
            ], 200),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'gemini']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Resposta vazia', $result->errorMessage);
    }

    public function test_generate_http_error()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'API key not valid'],
            ], 403),
        ]);

        $config = LlmConfig::factory()->create(['provider' => 'gemini']);
        $result = $this->provider->generate($config, 'prompt');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('API key', $result->errorMessage);
    }
}
