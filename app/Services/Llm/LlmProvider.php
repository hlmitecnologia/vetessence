<?php

namespace App\Services\Llm;

use App\Models\LlmConfig;

interface LlmProvider
{
    public function generate(LlmConfig $config, string $prompt): LlmResult;
}
