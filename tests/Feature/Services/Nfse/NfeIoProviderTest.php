<?php

namespace Tests\Feature\Services\Nfse;

use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Services\Nfse\NfeIoProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class NfeIoProviderTest extends ModuleTestCase
{
    protected NfeIoProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new NfeIoProvider;
    }

    public function test_emitir_success()
    {
        Http::fake([
            'api.nfe.io/v1/nfse' => Http::response([
                'nfse' => '123456',
                'codigo' => 'COD123',
                'xml' => 'https://fake.api/nfse/123456.xml',
                'pdf' => 'https://fake.api/nfse/123456.pdf',
            ], 201),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_emitir_api_error()
    {
        Http::fake([
            'api.nfe.io/v1/nfse' => Http::response([
                'erro' => 'CNPJ inválido',
            ], 422),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('CNPJ inválido', $result->errorMessage);
    }

    public function test_consultar_success()
    {
        Http::fake([
            'api.nfe.io/v1/nfse/123456' => Http::response([
                'nfse' => '123456',
                'codigo' => 'COD123',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);

        $result = $this->provider->consultar($config, '123456');

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_consultar_not_found()
    {
        Http::fake([
            'api.nfe.io/v1/nfse/000000' => Http::response([
                'erro' => 'NFSe não encontrada',
            ], 404),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);

        $result = $this->provider->consultar($config, '000000');

        $this->assertFalse($result->success);
    }

    public function test_cancelar_success()
    {
        Http::fake([
            'api.nfe.io/v1/nfse/123456/cancelar' => Http::response([
                'status' => 'cancelled',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);

        $result = $this->provider->cancelar($config, '123456', 'Cancelamento a pedido');

        $this->assertTrue($result->success);
    }

    public function test_cancelar_error()
    {
        Http::fake([
            'api.nfe.io/v1/nfse/123456/cancelar' => Http::response([
                'erro' => 'Prazo excedido',
            ], 422),
        ]);

        $config = NfseConfig::factory()->create(['nfeio_api_key' => 'test-api-key']);

        $result = $this->provider->cancelar($config, '123456', 'Teste');

        $this->assertFalse($result->success);
    }
}
