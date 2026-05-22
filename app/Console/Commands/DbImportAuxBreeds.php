<?php

namespace App\Console\Commands;

use App\Models\BreedDefault;
use Illuminate\Console\Command;

class DbImportAuxBreeds extends Command
{
    protected $signature = 'db:import-aux-breeds';

    protected $description = 'Import reptile and small_mammal breeds from local JSON files';

    private array $sources = [
        'reptile' => 'database/data/reptile_breeds.json',
        'small_mammal' => 'database/data/small_mammal_breeds.json',
    ];

    public function handle(): int
    {
        foreach ($this->sources as $species => $relativePath) {
            $path = base_path($relativePath);

            if (!file_exists($path)) {
                $this->error("File not found: {$path}");
                return Command::FAILURE;
            }

            $names = json_decode(file_get_contents($path), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Invalid JSON in {$path}: " . json_last_error_msg());
                return Command::FAILURE;
            }

            $bar = $this->output->createProgressBar(count($names));
            $bar->start();
            $bar->setMessage($species);

            $imported = 0;
            foreach ($names as $name) {
                BreedDefault::updateOrCreate(
                    ['species' => $species, 'breed' => $name],
                    ['species' => $species, 'breed' => $name, 'is_active' => true]
                );
                $imported++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->line("<fg=cyan>{$species}</>: {$imported} breeds imported.");
        }

        return Command::SUCCESS;
    }
}
