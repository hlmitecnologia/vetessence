<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\Branch;
use App\Models\NfeConfig;
use App\Models\NfeTransfer;
use App\Models\Product;
use App\Models\User;
use App\Services\Nfe\NfeService;
use App\Services\Nfe\NfeResult;
use App\Services\Nfe\WebmaniaProvider;
use Tests\ModuleTestCase;

class NfeTransferTest extends ModuleTestCase
{
    public function test_emitir_transferencia_cria_registro_de_nfe(): void
    {
        $product = Product::factory()->create(['cost_price' => 50.00]);
        $fromBranch = Branch::factory()->create(['cnpj' => '11222333000181', 'municipio_ibge' => '3550308']);
        $toBranch = Branch::factory()->create(['cnpj' => '11222333000181', 'municipio_ibge' => '3550308']);
        $user = User::factory()->create();

        NfeConfig::factory()->create([
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);

        $providerMock = \Mockery::mock(WebmaniaProvider::class);
        $providerMock->shouldReceive('emitirTransferencia')
            ->once()
            ->andReturn(NfeResult::success(
                nfeNumber: 'NFE-TRANSF-001',
                nfeKey: '22345678901234567890123456789012345678901234',
                xmlUrl: 'https://webmania.com.br/xml/nfe-transf-001.xml',
                pdfUrl: 'https://webmania.com.br/pdf/nfe-transf-001.pdf',
                danfeUrl: 'https://webmania.com.br/danfe/nfe-transf-001.pdf',
                rawResponse: ['status' => 'autorizado'],
            ));

        $service = new NfeService($providerMock);
        $result = $service->emitirTransferencia($product, $fromBranch, $toBranch, 10.0, $user);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-TRANSF-001', $result->nfeNumber);

        $this->assertDatabaseHas('nfe_transfers', [
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'nfe_number' => 'NFE-TRANSF-001',
            'status' => 'issued',
        ]);
    }

    public function test_emitir_transferencia_falha_nao_cria_registro(): void
    {
        $product = Product::factory()->create(['cost_price' => 50.00]);
        $fromBranch = Branch::factory()->create(['cnpj' => '11222333000181']);
        $toBranch = Branch::factory()->create(['cnpj' => '11222333000181']);
        $user = User::factory()->create();

        NfeConfig::factory()->create([
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
        ]);

        $providerMock = \Mockery::mock(WebmaniaProvider::class);
        $providerMock->shouldReceive('emitirTransferencia')
            ->once()
            ->andReturn(NfeResult::error('Erro na API Webmania'));

        $service = new NfeService($providerMock);
        $result = $service->emitirTransferencia($product, $fromBranch, $toBranch, 10.0, $user);

        $this->assertFalse($result->success);
        $this->assertEquals('Erro na API Webmania', $result->errorMessage);

        $this->assertDatabaseCount('nfe_transfers', 0);
    }

    public function test_emitir_transferencia_sem_config_retorna_erro(): void
    {
        $product = Product::factory()->create();
        $fromBranch = Branch::factory()->create();
        $toBranch = Branch::factory()->create();
        $user = User::factory()->create();

        $service = app(NfeService::class);
        $result = $service->emitirTransferencia($product, $fromBranch, $toBranch, 10.0, $user);

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não configurada para o sistema.', $result->errorMessage);
    }
}
