<?php

namespace App\Services\Llm;

class LlmResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $content = null,
        public readonly ?string $model = null,
        public readonly ?int $inputTokens = null,
        public readonly ?int $outputTokens = null,
        public readonly ?array $rawResponse = null,
        public readonly ?string $errorMessage = null,
    ) {}

    public static function success(
        string $content,
        string $model = '',
        int $inputTokens = 0,
        int $outputTokens = 0,
        array $rawResponse = [],
    ): self {
        return new self(
            success: true,
            content: $content,
            model: $model,
            inputTokens: $inputTokens,
            outputTokens: $outputTokens,
            rawResponse: $rawResponse,
        );
    }

    public static function error(string $message, ?array $rawResponse = null): self
    {
        return new self(
            success: false,
            errorMessage: $message,
            rawResponse: $rawResponse,
        );
    }
}
