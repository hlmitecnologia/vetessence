<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;
use Illuminate\Support\Facades\Http;

class OllamaProvider implements LlmProvider
{
    public function generate(LlmConfig $config, string $prompt): LlmResult
    {
        $baseUrl = rtrim($config->ollama_base_url ?: 'http://localhost:11434', '/');

        $response = Http::timeout(60)
            ->post("{$baseUrl}/api/chat", [
                'model' => $config->ollama_model ?: 'llama3',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um veterinário especialista em diagnóstico animal. Responda apenas em português brasileiro.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'options' => [
                    'temperature' => (float) ($config->temperature ?? 0.3),
                    'num_predict' => (int) ($config->max_tokens ?? 500),
                ],
                'stream' => false,
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return LlmResult::error(
                $body['error'] ?? 'Erro na API Ollama',
                $body
            );
        }

        return LlmResult::success(
            content: $body['message']['content'] ?? '',
            model: $body['model'] ?? '',
            inputTokens: $body['prompt_eval_count'] ?? 0,
            outputTokens: $body['eval_count'] ?? 0,
            rawResponse: $body,
        );
    }
}
