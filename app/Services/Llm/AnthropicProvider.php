<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;
use Illuminate\Support\Facades\Http;

class AnthropicProvider implements LlmProvider
{
    protected string $baseUrl = 'https://api.anthropic.com/v1';

    public function generate(LlmConfig $config, string $prompt): LlmResult
    {
        $response = Http::withHeaders([
            'x-api-key' => $config->anthropic_api_key,
            'anthropic-version' => '2023-06-01',
        ])
            ->timeout(30)
            ->post("{$this->baseUrl}/messages", [
                'model' => $config->anthropic_model ?: 'claude-3-haiku-20240307',
                'system' => 'Você é um veterinário especialista em diagnóstico animal. Responda apenas em português brasileiro.',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => (float) ($config->temperature ?? 0.3),
                'max_tokens' => (int) ($config->max_tokens ?? 500),
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return LlmResult::error(
                $body['error']['message'] ?? 'Erro na API Anthropic',
                $body
            );
        }

        return LlmResult::success(
            content: $body['content'][0]['text'] ?? '',
            model: $body['model'] ?? '',
            inputTokens: $body['usage']['input_tokens'] ?? 0,
            outputTokens: $body['usage']['output_tokens'] ?? 0,
            rawResponse: $body,
        );
    }
}
