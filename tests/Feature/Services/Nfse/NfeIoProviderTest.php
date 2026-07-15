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
            'api.nfe.io/v1/companies/company-123/serviceinvoices' => Http::response([
                'serviceInvoice' => [
                    'number' => 123456,
                    'verificationCode' => 'COD123',
                ],
            ], 201),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_emitir_api_error()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices' => Http::response([
                'errors' => [['message' => 'CNPJ inválido']],
            ], 422),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('CNPJ inválido', $result->errorMessage);
    }

    public function test_consultar_success()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/123456' => Http::response([
                'serviceInvoice' => [
                    'number' => 123456,
                    'verificationCode' => 'COD123',
                ],
            ], 200),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->consultar($config, '123456');

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
    }

    public function test_consultar_not_found()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/000000' => Http::response([
                'errors' => [['message' => 'NFSe não encontrada']],
            ], 404),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->consultar($config, '000000');

        $this->assertFalse($result->success);
    }

    public function test_cancelar_success()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/123456' => Http::response([
                'serviceInvoice' => [
                    'number' => 123456,
                    'flowStatus' => 'Cancelled',
                ],
            ], 200),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->cancelar($config, '123456', 'Cancelamento a pedido');

        $this->assertTrue($result->success);
    }

    public function test_cancelar_error()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/123456' => Http::response([
                'errors' => [['message' => 'Prazo excedido']],
            ], 422),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->cancelar($config, '123456', 'Teste');

        $this->assertFalse($result->success);
    }
}
