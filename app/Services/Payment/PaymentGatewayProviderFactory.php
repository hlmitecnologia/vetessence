<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentGatewayProvider;
use App\Services\Payment\Providers\MercadoPagoProvider;
use App\Services\Payment\Providers\PagSeguroProvider;
use App\Services\Payment\Providers\StoneProvider;
use App\Services\Payment\Providers\StripeProvider;
use App\Services\Payment\Providers\PixStaticProvider;
use InvalidArgumentException;

class PaymentGatewayProviderFactory
{
    protected array $map = [
        'mercadopago' => MercadoPagoProvider::class,
        'pagseguro'   => PagSeguroProvider::class,
        'stone'       => StoneProvider::class,
        'stripe'      => StripeProvider::class,
        'pix'         => PixStaticProvider::class,
    ];

    public function make(PaymentGateway $gateway): PaymentGatewayProvider
    {
        $class = $this->map[$gateway->provider] ?? null;

        if (!$class) {
            throw new InvalidArgumentException("Provider '{$gateway->provider}' não implementado.");
        }

        return new $class($gateway);
    }

    public function getWebhookUrl(PaymentGateway $gateway): string
    {
        return route('api.payments.webhook', ['gateway' => $gateway->id]);
    }

    public function registerProvider(string $key, string $class): void
    {
        $this->map[$key] = $class;
    }
}
