<?php

namespace App\Services\Notification\Contracts;

use App\Services\Notification\NotificationResult;

interface WhatsAppProvider
{
    public function send(string $from, string $to, string $message, ?string $mediaUrl = null): NotificationResult;
    public function getName(): string;
}
