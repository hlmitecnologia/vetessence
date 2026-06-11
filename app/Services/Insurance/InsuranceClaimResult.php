<?php

namespace App\Services\Insurance;

class InsuranceClaimResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $provider,
        public readonly ?string $externalId = null,
        public readonly ?string $message = null,
        public readonly ?array $rawResponse = null,
    ) {}

    public static function success(string $provider, string $externalId, ?array $rawResponse = null): self
    {
        return new self(true, $provider, $externalId, null, $rawResponse);
    }

    public static function failed(string $provider, string $message, ?array $rawResponse = null): self
    {
        return new self(false, $provider, null, $message, $rawResponse);
    }
}
