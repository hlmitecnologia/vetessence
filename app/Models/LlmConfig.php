<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LlmConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'is_active',
        'temperature',
        'max_tokens',
        'openai_api_key',
        'openai_model',
        'anthropic_api_key',
        'anthropic_model',
        'gemini_api_key',
        'gemini_model',
        'grok_api_key',
        'grok_model',
        'ollama_base_url',
        'ollama_model',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'temperature' => 'decimal:2',
        'max_tokens' => 'integer',
    ];
}
