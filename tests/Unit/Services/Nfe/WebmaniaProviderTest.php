<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Services\Nfe\WebmaniaProvider;
use App\Services\Nfe\NfeResult;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class WebmaniaProviderTest extends ModuleTestCase
{
    protected WebmaniaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = app(WebmaniaProvider::class);
    }

    public function test_emitir_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(WebmaniaProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(WebmaniaProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.webmania.base_url', 'https://webmania.com.br/api/1'));

        Http::fake([
            'https://webmania.com.br/api/1/nfe/emissao/' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-001.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfe-001.pdf',
                'danfe' => 'https://webmania.com.br/danfe/nfe-001.pdf',
            ], 201),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
        $this->assertEquals('https://webmania.com.br/xml/nfe-001.xml', $result->xmlUrl);
        $this->assertEquals('https://webmania.com.br/pdf/nfe-001.pdf', $result->pdfUrl);
        $this->assertEquals('https://webmania.com.br/danfe/nfe-001.pdf', $result->danfeUrl);
    }

    public function test_emitir_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(WebmaniaProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(WebmaniaProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.webmania.base_url', 'https://webmania.com.br/api/1'));

        Http::fake([
            'https://webmania.com.br/api/1/nfe/emissao/' => Http::response([
                'error' => 'Limite de NF-e mensal excedido',
            ], 429),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('Limite de NF-e mensal excedido', $result->errorMessage);
    }

    public function test_consultar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');

        Http::fake([
            'https://webmania.com.br/api/1/nfe/NFE-001/' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-001.xml',
                'status' => 'autorizado',
            ], 200),
        ]);

        $result = $this->provider->consultar($config, 'NFE-001');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
    }

    public function test_consultar_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');

        Http::fake([
            'https://webmania.com.br/api/1/nfe/INVALID/' => Http::response([
                'error' => 'NF-e não encontrada',
            ], 404),
        ]);

        $result = $this->provider->consultar($config, 'INVALID');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada', $result->errorMessage);
    }

    public function test_cancelar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');

        Http::fake([
            'https://webmania.com.br/api/1/nfe/cancelar/' => Http::response([
                'status' => 'cancelado',
            ], 200),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
    }

    public function test_cancelar_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');

        Http::fake([
            'https://webmania.com.br/api/1/nfe/cancelar/' => Http::response([
                'error' => 'Prazo de cancelamento expirado',
            ], 400),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('Prazo de cancelamento expirado', $result->errorMessage);
    }

    public function test_emitir_transferencia_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('access-token');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('access-token-secret');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');
        $config->shouldReceive('offsetExists')->andReturn(true);

        $fromBranch = \Mockery::mock(\App\Models\Branch::class);
        $fromBranch->shouldReceive('getAttribute')->with('cnpj')->andReturn('11222333000181');
        $fromBranch->shouldReceive('getAttribute')->with('ie')->andReturn('123456789');
        $fromBranch->shouldReceive('getAttribute')->with('crt')->andReturn('3');
        $fromBranch->shouldReceive('getAttribute')->with('municipio_ibge')->andReturn('3550308');
        $fromBranch->shouldReceive('getAttribute')->with('serie')->andReturn('1');
        $fromBranch->shouldReceive('getAttribute')->with('name')->andReturn('Filial Origem');
        $fromBranch->shouldReceive('getAttribute')->with('address')->andReturn('Rua A');
        $fromBranch->shouldReceive('getAttribute')->with('number')->andReturn('100');
        $fromBranch->shouldReceive('getAttribute')->with('neighborhood')->andReturn('Centro');
        $fromBranch->shouldReceive('getAttribute')->with('city')->andReturn('São Paulo');
        $fromBranch->shouldReceive('getAttribute')->with('state')->andReturn('SP');
        $fromBranch->shouldReceive('getAttribute')->with('zipcode')->andReturn('01001000');
        $fromBranch->shouldReceive('offsetExists')->andReturn(true);

        $toBranch = \Mockery::mock(\App\Models\Branch::class);
        $toBranch->shouldReceive('getAttribute')->with('cnpj')->andReturn('11222333000181');
        $toBranch->shouldReceive('getAttribute')->with('ie')->andReturn('987654321');
        $toBranch->shouldReceive('getAttribute')->with('name')->andReturn('Filial Destino');
        $toBranch->shouldReceive('getAttribute')->with('address')->andReturn('Rua B');
        $toBranch->shouldReceive('getAttribute')->with('number')->andReturn('200');
        $toBranch->shouldReceive('getAttribute')->with('neighborhood')->andReturn('Jardim');
        $toBranch->shouldReceive('getAttribute')->with('city')->andReturn('São Paulo');
        $toBranch->shouldReceive('getAttribute')->with('state')->andReturn('SP');
        $toBranch->shouldReceive('getAttribute')->with('zipcode')->andReturn('02002000');
        $toBranch->shouldReceive('offsetExists')->andReturn(true);

        $product = \Mockery::mock(\App\Models\Product::class);
        $product->shouldReceive('getAttribute')->with('name')->andReturn('Produto Teste');
        $product->shouldReceive('getAttribute')->with('ncm')->andReturn('30049099');
        $product->shouldReceive('getAttribute')->with('cest')->andReturn(null);
        $product->shouldReceive('getAttribute')->with('cfop')->andReturn('5949');
        $product->shouldReceive('getAttribute')->with('unit')->andReturn('UN');
        $product->shouldReceive('getAttribute')->with('cost_price')->andReturn(50.00);
        $product->shouldReceive('getAttribute')->with('cst')->andReturn('00');
        $product->shouldReceive('getAttribute')->with('csosn')->andReturn(null);
        $product->shouldReceive('offsetExists')->andReturn(true);

        $provider = \Mockery::mock(WebmaniaProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();

        $refBaseUrl = (new \ReflectionClass(WebmaniaProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.webmania.base_url', 'https://webmania.com.br/api/1'));

        Http::fake([
            'https://webmania.com.br/api/1/nfe/emissao/' => Http::response([
                'numero' => 'NFE-TRANSF-001',
                'chave' => '22345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-transf-001.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfe-transf-001.pdf',
                'danfe' => 'https://webmania.com.br/danfe/nfe-transf-001.pdf',
            ], 201),
        ]);

        $result = $provider->emitirTransferencia($config, [
            'product' => $product,
            'from_branch' => $fromBranch,
            'to_branch' => $toBranch,
            'quantity' => 10,
        ]);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-TRANSF-001', $result->nfeNumber);
    }

    public function test_headers_contains_all_required_fields(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('ck');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('cs');
        $config->shouldReceive('getAttribute')->with('webmania_access_token')->andReturn('at');
        $config->shouldReceive('getAttribute')->with('webmania_access_token_secret')->andReturn('ats');

        $refMethod = (new \ReflectionClass(WebmaniaProvider::class))->getMethod('headers');
        $refMethod->setAccessible(true);

        $headers = $refMethod->invoke($this->provider, $config);

        $this->assertEquals('ck', $headers['X-Consumer-Key']);
        $this->assertEquals('cs', $headers['X-Consumer-Secret']);
        $this->assertEquals('at', $headers['X-Access-Token']);
        $this->assertEquals('ats', $headers['X-Access-Token-Secret']);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }
}
