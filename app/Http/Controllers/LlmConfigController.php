<?php

namespace App\Http\Controllers;

use App\Models\LlmConfig;
use Illuminate\Http\Request;

class LlmConfigController extends Controller
{
    public function edit()
    {
        $config = LlmConfig::firstOrNew();

        return view('llm.config', compact('config'));
    }

    public function update(Request $request)
    {
        $rules = [
            'provider' => 'required|in:openai,anthropic,gemini,grok,ollama',
            'is_active' => 'nullable|boolean',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:4096',
        ];

        $provider = $request->input('provider', 'openai');

        $providerRules = match ($provider) {
            'openai' => [
                'openai_api_key' => 'required|string',
                'openai_model' => 'nullable|string',
            ],
            'anthropic' => [
                'anthropic_api_key' => 'required|string',
                'anthropic_model' => 'nullable|string',
            ],
            'gemini' => [
                'gemini_api_key' => 'required|string',
                'gemini_model' => 'nullable|string',
            ],
            'grok' => [
                'grok_api_key' => 'required|string',
                'grok_model' => 'nullable|string',
            ],
            'ollama' => [
                'ollama_base_url' => 'required|string',
                'ollama_model' => 'nullable|string',
            ],
            default => [],
        };

        $validated = $request->validate(array_merge($rules, $providerRules));

        LlmConfig::updateOrCreate(
            ['id' => LlmConfig::first()?->id],
            $validated + ['is_active' => $request->boolean('is_active')],
        );

        return redirect()
            ->route('llm.config')
            ->with('success', 'Configuração de IA salva!');
    }
}
