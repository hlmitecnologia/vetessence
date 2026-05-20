<?php

namespace Tests\Unit\Services\Nfse;

use App\Services\Nfse\NfseResult;
use Tests\TestCase;

class NfseResultTest extends TestCase
{
    public function test_success_result()
    {
        $result = NfseResult::success(
            nfseNumber: '123456',
            nfseCode: 'ABC123',
            xmlUrl: 'https://example.com/nfse.xml',
            pdfUrl: 'https://example.com/nfse.pdf',
            rpsNumber: '789012',
            verificationCode: 'ABCD-1234-EFGH-5678',
            rawResponse: ['id' => '1'],
        );

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
        $this->assertEquals('ABC123', $result->nfseCode);
        $this->assertEquals('https://example.com/nfse.xml', $result->xmlUrl);
        $this->assertEquals('https://example.com/nfse.pdf', $result->pdfUrl);
        $this->assertEquals('789012', $result->rpsNumber);
        $this->assertEquals('ABCD-1234-EFGH-5678', $result->verificationCode);
        $this->assertEquals(['id' => '1'], $result->rawResponse);
        $this->assertNull($result->errorMessage);
    }

    public function test_error_result()
    {
        $result = NfseResult::error('Erro ao emitir NFSe');

        $this->assertFalse($result->success);
        $this->assertEquals('Erro ao emitir NFSe', $result->errorMessage);
        $this->assertNull($result->nfseNumber);
        $this->assertNull($result->xmlUrl);
    }

    public function test_error_with_raw_response()
    {
        $result = NfseResult::error('Erro de validação', ['errors' => ['cnpj' => 'inválido']]);

        $this->assertFalse($result->success);
        $this->assertEquals('Erro de validação', $result->errorMessage);
        $this->assertEquals(['errors' => ['cnpj' => 'inválido']], $result->rawResponse);
    }

    public function test_readonly_properties()
    {
        $result = NfseResult::error('teste');
        $this->assertFalse($result->success);
    }
}
