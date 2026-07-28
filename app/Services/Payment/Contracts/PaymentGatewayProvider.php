<?php

namespace App\Services\Payment\Contracts;

use App\Models\Invoice;
use App\Models\PaymentGateway;

interface PaymentGatewayProvider
{
    public function charge(Invoice $invoice): array;

    public function checkout(Invoice $invoice): array;

    public function queryTransaction(string $identifier): array;

    public function cancelTransaction(string $identifier): bool;

    public function verifyWebhook(array $payload, PaymentGateway $gateway): ?array;

    public static function supportedChannels(): array;
}
