<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\DrugFormulary;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Console\Command;

class TreinamentoCleanup extends Command
{
    protected $signature = 'treinamento:cleanup {--module= : Module to clean up data for}';
    protected $description = 'Remove dados criados por roteiros de treinamento';

    public function handle(): void
    {
        $module = $this->option('module');

        if (! $module || $module === '07-farmacia') {
            $this->cleanupFarmacia();
        }

        $this->info('✅ Cleanup concluído.');
    }

    private function cleanupFarmacia(): void
    {
        $names = [
            'Medicamentos',
            'FarMed Distribuidora',
            'Dipirona 500mg',
            'Dipirona Sódica',
        ];
        $this->info('Limpando dados do roteiro 07-farmacia…');

        DrugFormulary::where('drug', 'Dipirona Sódica')->delete();
        $count = DrugFormulary::where('drug', 'Dipirona Sódica')->count();
        $this->line("  DrugFormulary removidos: {$count}");

        $prod = Product::where('name', 'Dipirona 500mg')->first();
        if ($prod) {
            StockMovement::where('product_id', $prod->id)->delete();
            $prod->delete();
        }
        $this->line('  Produto + movimentos de estoque removidos.');

        Supplier::where('name', 'FarMed Distribuidora')->delete();
        Category::where('name', 'Medicamentos')->delete();

        $this->line('  Categoria e fornecedor removidos.');
    }
}
