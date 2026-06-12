<?php

namespace Tests\Unit\Services\Nfe;

use App\Services\Nfe\NfeResult;
use Tests\ModuleTestCase;

class NfeResultTest extends ModuleTestCase
{
    public function test_success_factory_sets_all_fields(): void
    {
        $result = NfeResult::success(
            nfeNumber: 'NFE-001',
            nfeKey: '12345678901234567890123456789012345678901234',
            xmlUrl: 'https://example.com/nfe.xml',
            pdfUrl: 'https://example.com/nfe.pdf',
            danfeUrl: 'https://example.com/danfe.pdf',
            rawResponse: ['status' => 'autorizado'],
        );

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
        $this->assertEquals('https://example.com/nfe.xml', $result->xmlUrl);
        $this->assertEquals('https://example.com/nfe.pdf', $result->pdfUrl);
        $this->assertEquals('https://example.com/danfe.pdf', $result->danfeUrl);
        $this->assertEquals(['status' => 'autorizado'], $result->rawResponse);
        $this->assertNull($result->errorMessage);
    }

    public function test_success_defaults_to_empty_strings(): void
    {
        $result = NfeResult::success();

        $this->assertTrue($result->success);
        $this->assertEquals('', $result->nfeNumber);
        $this->assertEquals('', $result->nfeKey);
        $this->assertEquals('', $result->xmlUrl);
        $this->assertEquals('', $result->pdfUrl);
        $this->assertEquals('', $result->danfeUrl);
        $this->assertEquals([], $result->rawResponse);
        $this->assertNull($result->errorMessage);
    }

    public function test_error_factory_sets_error_message(): void
    {
        $result = NfeResult::error('Erro ao emitir NF-e');

        $this->assertFalse($result->success);
        $this->assertEquals('Erro ao emitir NF-e', $result->errorMessage);
        $this->assertNull($result->nfeNumber);
        $this->assertNull($result->nfeKey);
        $this->assertNull($result->xmlUrl);
        $this->assertNull($result->rawResponse);
    }

    public function test_error_with_raw_response(): void
    {
        $result = NfeResult::error('Erro de validação', ['errors' => ['cnpj' => 'inválido']]);

        $this->assertFalse($result->success);
        $this->assertEquals('Erro de validação', $result->errorMessage);
        $this->assertEquals(['errors' => ['cnpj' => 'inválido']], $result->rawResponse);
    }

    public function test_properties_are_readonly(): void
    {
        $result = NfeResult::success(nfeNumber: 'NFE-001');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
    }

    public function test_isSuccess_accessor(): void
    {
        $successResult = NfeResult::success();
        $errorResult = NfeResult::error('Erro');

        $this->assertTrue($successResult->success);
        $this->assertFalse($errorResult->success);
    }
}
