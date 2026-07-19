<?php

namespace Tests\Feature\Services\Nfse;

use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Services\Nfse\WebmaniaProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class WebmaniaProviderTest extends ModuleTestCase
{
    protected WebmaniaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new WebmaniaProvider;
    }

    public function test_emitir_success()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/emissao/' => Http::response([
                'nfse' => '123456',
                'codigo' => 'COD123',
                'xml' => 'https://webmania.com.br/xml/nfse-123456.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfse-123456.pdf',
                'rps' => 'RPS001',
                'codigo_verificacao' => 'ABCD-1234',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create();
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
        $this->assertEquals('COD123', $result->nfseCode);
    }

    public function test_emitir_api_error()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/emissao/' => Http::response([
                'error' => 'CNPJ inválido',
            ], 422),
        ]);

        $config = NfseConfig::factory()->create();
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('CNPJ inválido', $result->errorMessage);
    }

    public function test_emitir_server_error()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/emissao/' => Http::response(null, 500),
        ]);

        $config = NfseConfig::factory()->create();
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
    }

    public function test_consultar_success()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/123456/' => Http::response([
                'nfse' => '123456',
                'codigo' => 'COD123',
                'xml' => 'https://webmania.com.br/xml/nfse-123456.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfse-123456.pdf',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create();

        $result = $this->provider->consultar($config, '123456');

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_consultar_not_found()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/000000/' => Http::response([
                'error' => 'NFSe não encontrada',
            ], 404),
        ]);

        $config = NfseConfig::factory()->create();

        $result = $this->provider->consultar($config, '000000');

        $this->assertFalse($result->success);
    }

    public function test_cancelar_success()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/cancelar' => Http::response([
                'status' => 'cancelled',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create();

        $result = $this->provider->cancelar($config, '123456', 'Cancelamento a pedido do cliente');

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_cancelar_error()
    {
        Http::fake([
            'api.webmania.com.br/2/nfse/cancelar' => Http::response([
                'error' => 'Prazo de cancelamento excedido',
            ], 422),
        ]);

        $config = NfseConfig::factory()->create();

        $result = $this->provider->cancelar($config, '123456', 'Teste');

        $this->assertFalse($result->success);
    }

    public function test_headers_uses_bearer_token(): void
    {
        $config = NfseConfig::factory()->create([
            'webmania_access_token' => 'my-bearer-token',
        ]);

        $refMethod = (new \ReflectionClass(WebmaniaProvider::class))->getMethod('headers');
        $refMethod->setAccessible(true);

        $headers = $refMethod->invoke($this->provider, $config);

        $this->assertEquals('Bearer my-bearer-token', $headers['Authorization']);
    }
}
