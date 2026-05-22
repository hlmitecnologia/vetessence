<?php

namespace App\Services\Cep;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CepService
{
    protected int $timeout = 3;

    public function lookup(string $cep): ?CepResult
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        $cacheKey = "cep_{$cep}";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($cep) {
            $result = $this->lookupViaCep($cep);

            if (!$result) {
                $result = $this->lookupAwesomeApi($cep);
            }

            return $result;
        });
    }

    protected function lookupViaCep(string $cep): ?CepResult
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("https://viacep.com.br/ws/{$cep}/json/");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['erro']) && $data['erro'] === true) {
                return null;
            }

            return new CepResult(
                zipcode: $data['cep'] ?? $cep,
                street: $data['logradouro'] ?? '',
                neighborhood: $data['bairro'] ?? '',
                city: $data['localidade'] ?? '',
                state: $data['uf'] ?? '',
                ibge: $data['ibge'] ?? null,
            );
        } catch (\Throwable $e) {
            Log::warning('ViaCEP lookup failed', ['cep' => $cep, 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function lookupAwesomeApi(string $cep): ?CepResult
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("https://cep.awesomeapi.com.br/json/{$cep}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['error']) || empty($data['city'])) {
                return null;
            }

            return new CepResult(
                zipcode: $data['cep'] ?? $cep,
                street: $data['address'] ?? $data['address_name'] ?? '',
                neighborhood: $data['neighborhood'] ?? '',
                city: $data['city'] ?? '',
                state: $data['state'] ?? '',
                ibge: $data['city_ibge'] ?? null,
            );
        } catch (\Throwable $e) {
            Log::warning('AwesomeAPI CEP lookup failed', ['cep' => $cep, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
