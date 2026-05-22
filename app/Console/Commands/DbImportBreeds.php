<?php

namespace App\Console\Commands;

use App\Models\BreedDefault;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbImportBreeds extends Command
{
    protected $signature = 'db:import-breeds';
    protected $description = 'Import breeds from megapedigreedb into breed_defaults';

    public function handle(): int
    {
        if (!DB::connection('megapedigreedb')->getDatabaseName()) {
            $this->error('megapedigreedb connection not available.');
            return Command::FAILURE;
        }

        $rows = DB::connection('megapedigreedb')
            ->table('racas')
            ->where('ativo', 1)
            ->select('id', 'nomeraca')
            ->orderBy('nomeraca')
            ->get();

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $imported = 0;
        foreach ($rows as $row) {
            $name = mb_convert_case(mb_strtolower(trim($row->nomeraca)), MB_CASE_TITLE, 'UTF-8');
            if (empty($name)) {
                continue;
            }
            BreedDefault::updateOrCreate(
                ['species' => 'canine', 'breed' => $name],
                ['species' => 'canine', 'breed' => $name, 'is_active' => true]
            );
            $imported++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$imported} breeds imported.");

        return Command::SUCCESS;
    }
}
