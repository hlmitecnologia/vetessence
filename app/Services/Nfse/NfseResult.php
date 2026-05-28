<?php

namespace App\Services\Nfse;

class NfseResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $nfseNumber = null,
        public readonly ?string $nfseCode = null,
        public readonly ?string $xmlUrl = null,
        public readonly ?string $pdfUrl = null,
        public readonly ?string $rpsNumber = null,
        public readonly ?string $verificationCode = null,
        public readonly ?array $rawResponse = null,
        public readonly ?string $errorMessage = null,
    ) {}

    public static function success(
        string $nfseNumber = '',
        string $nfseCode = '',
        string $xmlUrl = '',
        string $pdfUrl = '',
        string $rpsNumber = '',
        string $verificationCode = '',
        array $rawResponse = [],
    ): self {
        return new self(
            success: true,
            nfseNumber: $nfseNumber,
            nfseCode: $nfseCode,
            xmlUrl: $xmlUrl,
            pdfUrl: $pdfUrl,
            rpsNumber: $rpsNumber,
            verificationCode: $verificationCode,
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
