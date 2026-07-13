<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\NfeConfig;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class StockTransferNfeTest extends ModuleTestCase
{
    public function test_transferencia_sem_nfe(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('stock.transfer');

        $product = Product::factory()->create(['stock' => 100]);
        $fromBranch = Branch::factory()->create();
        $toBranch = Branch::factory()->create();

        $response = $this->actingAs($user)->post(route('stock.transfer'), [
            'product_id' => $product->id,
            'quantity' => 10,
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
        ]);

        $response->assertRedirect(route('stock.movements'));
        $response->assertSessionHas('success', 'Transferência realizada com sucesso.');

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'transfer_out',
            'branch_id' => $fromBranch->id,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'transfer_in',
            'branch_id' => $toBranch->id,
        ]);
    }

    public function test_transferencia_com_nfe_sucesso(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('stock.transfer');

        $product = Product::factory()->create(['stock' => 100, 'cost_price' => 50.00]);
        $fromBranch = Branch::factory()->create([
            'cnpj' => '11222333000181',
            'municipio_ibge' => '3550308',
        ]);
        $toBranch = Branch::factory()->create([
            'cnpj' => '11222333000181',
            'municipio_ibge' => '3550308',
        ]);

        NfeConfig::factory()->create([
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
            'webmania_consumer_key' => 'ck',
            'webmania_consumer_secret' => 'cs',
            'webmania_access_token' => 'at',
            'webmania_access_token_secret' => 'ats',
        ]);

        Http::fake([
            'webmania.com.br/api/1/nfe/emissao/' => Http::response([
                'numero' => 'NFE-TRANSF-001',
                'chave' => '22345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-transf-001.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfe-transf-001.pdf',
                'danfe' => 'https://webmania.com.br/danfe/nfe-transf-001.pdf',
            ], 201),
        ]);

        $response = $this->actingAs($user)->post(route('stock.transfer'), [
            'product_id' => $product->id,
            'quantity' => 10,
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
            'emitir_nfe' => '1',
        ]);

        $response->assertRedirect(route('stock.movements'));
        $response->assertSessionHas('success', 'Transferência realizada com sucesso.');

        $this->assertDatabaseHas('nfe_transfers', [
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
            'product_id' => $product->id,
            'nfe_number' => 'NFE-TRANSF-001',
            'status' => 'issued',
        ]);
    }

    public function test_transferencia_com_nfe_falha_avisa_mas_nao_impede(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('stock.transfer');

        $product = Product::factory()->create(['stock' => 100, 'cost_price' => 50.00]);
        $fromBranch = Branch::factory()->create([
            'cnpj' => '11222333000181',
            'municipio_ibge' => '3550308',
        ]);
        $toBranch = Branch::factory()->create([
            'cnpj' => '11222333000181',
            'municipio_ibge' => '3550308',
        ]);

        NfeConfig::factory()->create([
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'is_active' => true,
            'webmania_consumer_key' => 'ck',
            'webmania_consumer_secret' => 'cs',
            'webmania_access_token' => 'at',
            'webmania_access_token_secret' => 'ats',
        ]);

        Http::fake([
            'webmania.com.br/api/1/nfe/emissao/' => Http::response([
                'error' => 'Limite de NF-e excedido',
            ], 429),
        ]);

        $response = $this->actingAs($user)->post(route('stock.transfer'), [
            'product_id' => $product->id,
            'quantity' => 10,
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
            'emitir_nfe' => '1',
        ]);

        $response->assertRedirect(route('stock.movements'));
        $response->assertSessionHas('warning');

        $this->assertDatabaseCount('nfe_transfers', 0);
    }
}
