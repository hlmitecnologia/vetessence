<?php

namespace Tests\Unit\Services\Llm;

use App\Services\Llm\LlmResult;
use Tests\TestCase;

class LlmResultTest extends TestCase
{
    public function test_success_result()
    {
        $result = LlmResult::success(
            content: 'Diagnóstico: Cinomose',
            model: 'gpt-4o-mini',
            inputTokens: 150,
            outputTokens: 50,
            rawResponse: ['id' => 'chatcmpl-123'],
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Diagnóstico: Cinomose', $result->content);
        $this->assertEquals('gpt-4o-mini', $result->model);
        $this->assertEquals(150, $result->inputTokens);
        $this->assertEquals(50, $result->outputTokens);
        $this->assertEquals(['id' => 'chatcmpl-123'], $result->rawResponse);
        $this->assertNull($result->errorMessage);
    }

    public function test_error_result()
    {
        $result = LlmResult::error('Erro na API OpenAI');

        $this->assertFalse($result->success);
        $this->assertEquals('Erro na API OpenAI', $result->errorMessage);
        $this->assertNull($result->content);
        $this->assertNull($result->model);
    }

    public function test_error_with_raw_response()
    {
        $result = LlmResult::error('Chave inválida', ['error' => ['code' => 'invalid_api_key']]);

        $this->assertFalse($result->success);
        $this->assertEquals('Chave inválida', $result->errorMessage);
        $this->assertEquals(['error' => ['code' => 'invalid_api_key']], $result->rawResponse);
    }

    public function test_readonly_properties()
    {
        $result = LlmResult::success(content: 'ok');
        $this->assertTrue($result->success);
    }

    public function test_success_with_defaults()
    {
        $result = LlmResult::success(content: 'teste');

        $this->assertTrue($result->success);
        $this->assertEquals('teste', $result->content);
        $this->assertEquals('', $result->model);
        $this->assertEquals(0, $result->inputTokens);
        $this->assertEquals(0, $result->outputTokens);
        $this->assertEquals([], $result->rawResponse);
    }

    public function test_error_can_receive_null_raw()
    {
        $result = LlmResult::error('falhou', null);
        $this->assertFalse($result->success);
        $this->assertNull($result->rawResponse);
    }
}
