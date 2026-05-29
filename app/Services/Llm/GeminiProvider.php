<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements LlmProvider
{
    public function generate(LlmConfig $config, string $prompt): LlmResult
    {
        $apiKey = $config->gemini_api_key;
        $model = $config->gemini_model ?: 'gemini-2.0-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(30)
            ->post($url, [
                'contents' => [
                    'parts' => [
                        ['text' => "Você é um veterinário especialista em diagnóstico animal. Responda apenas em português brasileiro.\n\n{$prompt}"],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => (float) ($config->temperature ?? 0.3),
                    'maxOutputTokens' => (int) ($config->max_tokens ?? 500),
                ],
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return LlmResult::error(
                $body['error']['message'] ?? 'Erro na API Gemini',
                $body
            );
        }

        $candidate = $body['candidates'][0] ?? null;

        if (!$candidate) {
            return LlmResult::error('Resposta vazia da API Gemini.', $body);
        }

        $finishReason = $candidate['finishReason'] ?? null;
        if ($finishReason === 'MAX_TOKENS') {
            return LlmResult::error(
                'O limite de tokens do modelo Gemini foi excedido. O prompt é muito longo para o modelo configurado.',
                $body
            );
        }

        $content = '';
        if (isset($body['candidates'][0]['content']['parts'])) {
            $parts = $body['candidates'][0]['content']['parts'];
            $content = collect($parts)->pluck('text')->implode("\n");
        }

        return LlmResult::success(
            content: $content,
            model: $model,
            rawResponse: $body,
        );
    }
}
