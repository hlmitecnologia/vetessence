<?php

namespace Tests\Unit\Services\Cep;

use App\Services\Cep\CepService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CepServiceTest extends TestCase
{
    protected CepService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CepService::class);
    }

    public function test_returns_null_for_invalid_cep(): void
    {
        $this->assertNull($this->service->lookup('123'));
        $this->assertNull($this->service->lookup(''));
        $this->assertNull($this->service->lookup('abcdefgh'));
    }

    public function test_returns_null_for_cep_with_wrong_length(): void
    {
        $this->assertNull($this->service->lookup('1234567'));
        $this->assertNull($this->service->lookup('123456789'));
    }

    public function test_returns_data_from_viacep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'bairro' => 'Bela Vista',
                'localidade' => 'São Paulo',
                'uf' => 'SP',
                'ibge' => '3550308',
            ], 200),
        ]);

        $result = $this->service->lookup('01310100');

        $this->assertNotNull($result);
        $this->assertEquals('Avenida Paulista', $result->street);
        $this->assertEquals('Bela Vista', $result->neighborhood);
        $this->assertEquals('São Paulo', $result->city);
        $this->assertEquals('SP', $result->state);
        $this->assertEquals('01310-100', $result->zipcode);
        $this->assertEquals('3550308', $result->ibge);
    }

    public function test_strips_non_digits_from_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'bairro' => 'Bela Vista',
                'localidade' => 'São Paulo',
                'uf' => 'SP',
            ], 200),
        ]);

        $result = $this->service->lookup('01310-100');

        $this->assertNotNull($result);
        $this->assertEquals('Avenida Paulista', $result->street);
    }

    public function test_uses_awesomeapi_fallback_when_viacep_fails(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response('', 500),
            'https://cep.awesomeapi.com.br/json/01310100' => Http::response([
                'cep' => '01310100',
                'address' => 'Avenida Paulista',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
                'city_ibge' => '3550308',
            ], 200),
        ]);

        $result = $this->service->lookup('01310100');

        $this->assertNotNull($result);
        $this->assertEquals('Avenida Paulista', $result->street);
        $this->assertEquals('Bela Vista', $result->neighborhood);
        $this->assertEquals('São Paulo', $result->city);
        $this->assertEquals('SP', $result->state);
    }

    public function test_returns_null_when_both_apis_fail(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response('', 500),
            'https://cep.awesomeapi.com.br/json/01310100' => Http::response('', 500),
        ]);

        $result = $this->service->lookup('01310100');

        $this->assertNull($result);
    }

    public function test_returns_null_for_inexistent_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/99999999/json/' => Http::response(['erro' => true], 200),
        ]);

        $result = $this->service->lookup('99999999');

        $this->assertNull($result);
    }

    public function test_awesomeapi_returns_null_when_response_has_error(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/01310100/json/' => Http::response('', 500),
            'https://cep.awesomeapi.com.br/json/01310100' => Http::response(['error' => true], 200),
        ]);

        $result = $this->service->lookup('01310100');

        $this->assertNull($result);
    }

    public function test_caches_result(): void
    {
        $cacheKey = 'cep_01310100';

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, \Mockery::type(\DateTimeInterface::class), \Mockery::on(function ($callback) {
                return is_callable($callback);
            }))
            ->andReturn(null);

        $result = $this->service->lookup('01310100');
    }
}
