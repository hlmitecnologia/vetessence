<?php

namespace App\Services\Nfe;

class NfeResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $nfeNumber = null,
        public readonly ?string $nfeKey = null,
        public readonly ?string $xmlUrl = null,
        public readonly ?string $pdfUrl = null,
        public readonly ?string $danfeUrl = null,
        public readonly ?array $rawResponse = null,
        public readonly ?string $errorMessage = null,
    ) {}

    public static function success(
        string $nfeNumber = '',
        string $nfeKey = '',
        string $xmlUrl = '',
        string $pdfUrl = '',
        string $danfeUrl = '',
        array $rawResponse = [],
    ): self {
        return new self(
            success: true,
            nfeNumber: $nfeNumber,
            nfeKey: $nfeKey,
            xmlUrl: $xmlUrl,
            pdfUrl: $pdfUrl,
            danfeUrl: $danfeUrl,
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
