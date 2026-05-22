<?php

namespace App\Services\Cep;

class CepResult
{
    public function __construct(
        public readonly string $zipcode,
        public readonly string $street,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly ?string $ibge = null,
    ) {}

    public function stateUf(): string
    {
        return strtoupper($this->state);
    }

    public function toArray(): array
    {
        return [
            'zipcode' => $this->zipcode,
            'street' => $this->street,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'ibge' => $this->ibge,
        ];
    }
}
