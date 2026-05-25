<?php

namespace App\Services\Notification\Contracts;

use App\Services\Notification\NotificationResult;

interface EmailProvider
{
    public function send(string $from, string $to, string $subject, string $body, array $attachments = []): NotificationResult;
    public function getName(): string;
}
