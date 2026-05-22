<?php

namespace App\Console\Commands;

use App\Models\BreedDefault;
use Illuminate\Console\Command;

class DbImportVboBreeds extends Command
{
    protected $signature = 'db:import-vbo-breeds
        {--path= : Path to the VBO JSON file}';

    protected $description = 'Import all breeds from VBO (Vertebrate Breed Ontology) into breed_defaults';

    public function handle(): int
    {
        $path = $this->option('path')
            ?? database_path('data/vbo_breeds.json');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            $this->line('Run php artisan vbo:extract first to generate the JSON file.');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $total = 0;
        foreach ($data as $species => $breeds) {
            $count = 0;
            $bar = $this->output->createProgressBar(count($breeds));
            $bar->setFormat("<fg=cyan>%message%</>: %current%/%max% [%bar%] %percent:3s%%");
            $bar->setMessage($species);
            $bar->start();

            foreach ($breeds as $name) {
                BreedDefault::updateOrCreate(
                    ['species' => $species, 'breed' => $name],
                    ['species' => $species, 'breed' => $name, 'is_active' => true]
                );
                $count++;
                $bar->advance();
            }
            $total += $count;

            $bar->finish();
            $this->newLine();
        }

        $this->info("{$total} breeds imported across " . count($data) . ' species.');

        return Command::SUCCESS;
    }
}
