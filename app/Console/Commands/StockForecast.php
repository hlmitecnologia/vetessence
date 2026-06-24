<?php

namespace App\Console\Commands;

use App\Services\StockForecastService;
use Illuminate\Console\Command;

class StockForecast extends Command
{
    protected $signature = 'stock:forecast
        {--recalculate : Recalcula consumo médio + reorder point de todos os produtos}
        {--alert-expiry : Dispara alertas de vencimento (próximos 30 dias)}';

    protected $description = 'Gestão preditiva de estoque';

    public function handle(StockForecastService $service): int
    {
        $recalculate = $this->option('recalculate');
        $alertExpiry = $this->option('alert-expiry');

        if (!$recalculate && !$alertExpiry) {
            $this->error('Informe --recalculate ou --alert-expiry');
            return Command::FAILURE;
        }

        if ($recalculate) {
            $this->info('Recalculando consumo médio e ponto de reposição...');
            $result = $service->recalculateAll();
            $this->info("{$result['updated']} produtos atualizados.");
        }

        if ($alertExpiry) {
            $this->info('Verificando produtos próximos ao vencimento...');
            $expiring = $service->expiringProducts(30);

            if ($expiring->isEmpty()) {
                $this->info('Nenhum produto próximo ao vencimento.');
            } else {
                $this->table(
                    ['Produto', 'Lote', 'Validade', 'Estoque', 'Valor Total'],
                    $expiring->map(fn ($item) => [
                        $item->product->name,
                        $item->batch_number ?? '-',
                        $item->product->expiration_date->format('d/m/Y'),
                        $item->product->stock,
                        'R$ ' . number_format($item->total_value, 2, ',', '.'),
                    ])
                );
            }
        }

        return Command::SUCCESS;
    }
}
