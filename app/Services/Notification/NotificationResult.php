<?php

namespace App\Services\Notification;

class NotificationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $provider,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
    ) {}

    public static function success(string $provider, ?string $messageId = null): self
    {
        return new self(true, $provider, $messageId);
    }

    public static function failed(string $provider, string $error): self
    {
        return new self(false, $provider, null, $error);
    }
}
