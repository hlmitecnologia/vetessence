<?php

namespace App\Services\Insurance;

use InvalidArgumentException;

class InsuranceProviderFactory
{
    private static array $providers = [
        'porto-seguro' => PortoSeguroProvider::class,
    ];

    public static function make(string $provider): InsuranceProvider
    {
        $class = self::$providers[$provider] ?? null;

        if (!$class) {
            throw new InvalidArgumentException("Operadora de convênio desconhecida: {$provider}");
        }

        return app($class);
    }

    public static function register(string $name, string $class): void
    {
        self::$providers[$name] = $class;
    }
}
