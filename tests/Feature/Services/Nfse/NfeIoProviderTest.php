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

    public function test_buildPayload_includes_phoneNumber_when_tutor_has_phone()
    {
        Http::fake();

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $tutor = \App\Models\Tutor::factory()->create([
            'phone' => '(11) 98765-0101',
            'cpf' => '12345678901',
            'name' => 'Test Tutor',
            'email' => 'tutor@test.com',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 100.00,
        ]);

        $result = $this->provider->emitir($config, $invoice);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['borrower']['phoneNumber'])
                && $body['borrower']['phoneNumber'] === '11987650101';
        });
    }

    public function test_buildPayload_pads_phoneNumber_when_tutor_and_branch_have_no_phone()
    {
        Http::fake();

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $tutor = \App\Models\Tutor::factory()->create([
            'phone' => '',
            'cpf' => '12345678901',
            'name' => 'Test Tutor',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'total' => 100.00,
        ]);

        $this->provider->emitir($config, $invoice);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['borrower']['phoneNumber']) && $body['borrower']['phoneNumber'] === '00000000';
        });
    }

    public function test_buildPayload_falls_back_to_branch_phone_when_tutor_phone_too_short()
    {
        Http::fake();

        $branch = \App\Models\Branch::factory()->create([
            'phone' => '(11) 3333-4444',
        ]);
        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $tutor = \App\Models\Tutor::factory()->create([
            'phone' => '1234',
            'cpf' => '12345678901',
            'name' => 'Test Tutor',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'branch_id' => $branch->id,
            'total' => 100.00,
        ]);

        $this->provider->emitir($config, $invoice);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['borrower']['phoneNumber'])
                && $body['borrower']['phoneNumber'] === '1133334444';
        });
    }

    public function test_emitir_success()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices' => Http::response([
                'id' => 'nfse-uuid-123',
                'number' => 123456,
                'checkCode' => 'COD123',
                'rpsNumber' => 789,
                'flowStatus' => 'Issued',
            ], 201),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);
        $invoice = Invoice::factory()->create();

        $result = $this->provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('nfse-uuid-123', $result->nfseNumber);
        $this->assertEquals('123456', $result->nfseCode);
        $this->assertEquals('COD123', $result->verificationCode);
        $this->assertEquals('789', $result->rpsNumber);
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
            'api.nfe.io/v1/companies/company-123/serviceinvoices/nfse-uuid-123' => Http::response([
                'id' => 'nfse-uuid-123',
                'number' => 123456,
                'checkCode' => 'COD123',
                'rpsNumber' => 789,
            ], 200),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->consultar($config, 'nfse-uuid-123');

        $this->assertTrue($result->success);
        $this->assertEquals('nfse-uuid-123', $result->nfseNumber);
        $this->assertEquals('123456', $result->nfseCode);
    }

    public function test_consultar_not_found()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/unknown-uuid' => Http::response([
                'errors' => [['message' => 'NFSe não encontrada']],
            ], 404),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->consultar($config, 'unknown-uuid');

        $this->assertFalse($result->success);
    }

    public function test_cancelar_success()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/nfse-uuid-123' => Http::response([
                'id' => 'nfse-uuid-123',
                'number' => 123456,
                'flowStatus' => 'Cancelled',
            ], 200),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->cancelar($config, 'nfse-uuid-123', 'Cancelamento a pedido');

        $this->assertTrue($result->success);
        $this->assertEquals('nfse-uuid-123', $result->nfseNumber);
    }

    public function test_cancelar_error()
    {
        Http::fake([
            'api.nfe.io/v1/companies/company-123/serviceinvoices/nfse-uuid-123' => Http::response([
                'errors' => [['message' => 'Prazo excedido']],
            ], 422),
        ]);

        $config = NfseConfig::factory()->create([
            'nfeio_api_key' => 'test-api-key',
            'nfeio_company_id' => 'company-123',
        ]);

        $result = $this->provider->cancelar($config, 'nfse-uuid-123', 'Teste');

        $this->assertFalse($result->success);
    }
}
