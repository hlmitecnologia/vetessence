<?php

namespace Tests\Unit\Services\Cep;

use App\Services\Cep\CepResult;
use Tests\TestCase;

class CepResultTest extends TestCase
{
    public function test_creates_with_all_fields(): void
    {
        $result = new CepResult(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            neighborhood: 'Bela Vista',
            city: 'São Paulo',
            state: 'SP',
            ibge: '3550308',
        );

        $this->assertEquals('01310100', $result->zipcode);
        $this->assertEquals('Avenida Paulista', $result->street);
        $this->assertEquals('Bela Vista', $result->neighborhood);
        $this->assertEquals('São Paulo', $result->city);
        $this->assertEquals('SP', $result->state);
        $this->assertEquals('3550308', $result->ibge);
    }

    public function test_creates_without_ibge(): void
    {
        $result = new CepResult('01001000', 'Rua Exemplo', 'Centro', 'São Paulo', 'SP');

        $this->assertEquals('01001000', $result->zipcode);
        $this->assertNull($result->ibge);
    }

    public function test_state_uf_returns_uppercase(): void
    {
        $result = new CepResult('01001000', '', '', '', 'sp');
        $this->assertEquals('SP', $result->stateUf());
    }

    public function test_state_uf_with_already_uppercase(): void
    {
        $result = new CepResult('01001000', '', '', '', 'SP');
        $this->assertEquals('SP', $result->stateUf());
    }

    public function test_to_array_returns_all_fields(): void
    {
        $result = new CepResult('01310100', 'Av Paulista', 'Bela Vista', 'São Paulo', 'SP', '3550308');

        $array = $result->toArray();

        $this->assertEquals([
            'zipcode' => '01310100',
            'street' => 'Av Paulista',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'ibge' => '3550308',
        ], $array);
    }

    public function test_to_array_without_ibge(): void
    {
        $result = new CepResult('01001000', 'Rua Exemplo', 'Centro', 'São Paulo', 'SP');

        $array = $result->toArray();

        $this->assertArrayHasKey('ibge', $array);
        $this->assertNull($array['ibge']);
    }
}
