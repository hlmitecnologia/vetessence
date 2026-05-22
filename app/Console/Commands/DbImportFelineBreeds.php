<?php

namespace App\Console\Commands;

use App\Models\BreedDefault;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbImportFelineBreeds extends Command
{
    protected $signature = 'db:import-feline-breeds';
    protected $description = 'Import feline breeds from algcdb into breed_defaults';

    public function handle(): int
    {
        if (!DB::connection('algcdb')->getDatabaseName()) {
            $this->error('algcdb connection not available.');
            return Command::FAILURE;
        }

        $rows = DB::connection('algcdb')
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
                ['species' => 'feline', 'breed' => $name],
                ['species' => 'feline', 'breed' => $name, 'is_active' => true]
            );
            $imported++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$imported} feline breeds imported.");

        return Command::SUCCESS;
    }
}
