<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Vaccination;

class StockDeductionService
{
    public function deductFromVaccination(Vaccination $vaccination): void
    {
        if (!$vaccination->product_id) return;

        $product = Product::find($vaccination->product_id);
        if (!$product) return;

        if ($product->stock < 1) {
            throw new \RuntimeException("Estoque insuficiente de {$product->name} para dar baixa.");
        }

        StockMovement::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'type' => 'out',
            'branch_id' => $vaccination->branch_id ?? auth()->user()->branch_id,
            'user_id' => auth()->id(),
            'notes' => "Baixa automática - Vacinação: {$vaccination->vaccine} - Pet #{$vaccination->pet_id}",
        ]);
    }
}
