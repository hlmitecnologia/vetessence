<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;
use Illuminate\Support\Facades\Http;

class OpenAiProvider implements LlmProvider
{
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function generate(LlmConfig $config, string $prompt): LlmResult
    {
        $response = Http::withToken($config->openai_api_key)
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $config->openai_model ?: 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um veterinário especialista em diagnóstico animal. Responda apenas em português brasileiro.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => (float) ($config->temperature ?? 0.3),
                'max_tokens' => (int) ($config->max_tokens ?? 500),
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return LlmResult::error(
                $body['error']['message'] ?? 'Erro na API OpenAI',
                $body
            );
        }

        return LlmResult::success(
            content: $body['choices'][0]['message']['content'] ?? '',
            model: $body['model'] ?? '',
            inputTokens: $body['usage']['prompt_tokens'] ?? 0,
            outputTokens: $body['usage']['completion_tokens'] ?? 0,
            rawResponse: $body,
        );
    }
}
