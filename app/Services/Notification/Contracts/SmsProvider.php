<?php

namespace App\Services\Notification\Contracts;

use App\Services\Notification\NotificationResult;

interface SmsProvider
{
    public function send(string $from, string $to, string $message): NotificationResult;
    public function getName(): string;
}
