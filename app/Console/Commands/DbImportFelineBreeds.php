<?php

namespace App\Console\Commands;

use App\Models\BreedDefault;
use Illuminate\Console\Command;

class DbImportFelineBreeds extends Command
{
    protected $signature = 'db:import-feline-breeds
        {--path= : Path to the feline breeds JSON file}';

    protected $description = 'Import feline breeds into breed_defaults from a local JSON file';

    public function handle(): int
    {
        $path = $this->option('path')
            ?? database_path('data/feline_breeds.json');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return Command::FAILURE;
        }

        $names = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($names));
        $bar->start();

        $imported = 0;
        foreach ($names as $name) {
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
