<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ExtractVboBreeds extends Command
{
    protected $signature = 'vbo:extract
        {--csv= : Path to VBO CSV file (downloads if not provided)}';

    protected $description = 'Download VBO CSV from BioPortal and extract canonical breed names by species';

    private string $csvUrl = 'https://data.bioontology.org/ontologies/VBO/download?apikey=8b5b7825-538d-40e0-9e9e-5ab9274a9aeb&download_format=csv';

    private array $speciesMap = [
        'Dog breed' => 'canine', 'Cat breed' => 'feline',
        'Cattle breed' => 'bovine', 'Bovine breed' => 'bovine',
        'Equid breed' => 'equine', 'Sheep breed' => 'ovine',
        'Goat breed' => 'caprine', 'Bird breed' => 'avian',
        'Chicken breed' => 'avian', 'Duck breed' => 'avian',
        'Goose breed' => 'avian', 'Turkey breed' => 'avian',
        'Guinea fowl breed' => 'avian', 'Pheasant breed' => 'avian',
        'Partridge breed' => 'avian', 'Ostrich breed' => 'avian',
        'Pigeon breed' => 'avian', 'Zebra finch breed' => 'avian',
        'Rabbit breed' => 'lagomorph', 'Guinea pig breed' => 'rodent',
        'Golden hamster breed' => 'rodent', 'Fish breed' => 'fish',
        'Rainbow trout breed' => 'fish', 'Zebrafish breed' => 'fish',
        'Common carp breed' => 'fish', 'Goldfish breed' => 'fish',
        'Japanese rice fish breed' => 'fish', 'Amphibian breed' => 'amphibian',
        'Frog breed' => 'amphibian', 'Camel breed' => 'camelid',
        'South American camelid breed' => 'camelid', 'Deer breed' => 'cervid',
        'Ass breed' => 'asinine', 'Mammalian breed' => 'exotic',
        'Vertebrate breed' => 'exotic',
    ];

    public function handle(): int
    {
        $csvPath = $this->option('csv');

        if (!$csvPath) {
            $this->info('Downloading VBO CSV from BioPortal...');
            $csvPath = sys_get_temp_dir() . '/vbo_download.csv';

            $response = Http::timeout(120)->get($this->csvUrl);
            if (!$response->ok()) {
                $this->error('Download failed: ' . $response->status());
                return Command::FAILURE;
            }
            file_put_contents($csvPath, $response->body());
            $this->info('Downloaded');
        }

        if (!file_exists($csvPath)) {
            $this->error("File not found: {$csvPath}");
            return Command::FAILURE;
        }

        $this->info('Parsing CSV...');
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->error('Could not open file');
            return Command::FAILURE;
        }

        $headers = fgetcsv($handle);
        $labelIdx = 1;
        $parentsIdx = 7;

        $vbo040Labels = [];
        rewind($handle);
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $cid = $row[0] ?? '';
            if (str_contains($cid, '/VBO_04')) {
                $vbo040Labels[$cid] = $row[$labelIdx] ?? '';
            }
        }

        $breedsBySpecies = [];

        rewind($handle);
        fgetcsv($handle);
        $pattern = '/^(.+?)\s+\((.+)\)$/';

        while (($row = fgetcsv($handle)) !== false) {
            $label = $row[$labelIdx] ?? '';
            $parentsStr = $row[$parentsIdx] ?? '';

            if (!preg_match($pattern, $label, $m)) {
                continue;
            }

            $breedName = trim($m[1]);

            if (str_contains($breedName, ',')) {
                continue;
            }

            $lower = mb_strtolower($breedName);
            if (in_array($lower, ['breed', 'standard', 'crossbreed', 'mixed', 'unknown', 'feral', ''], true) || mb_strlen($breedName) < 2) {
                continue;
            }

            $vboSpecies = null;
            foreach (explode('|', $parentsStr) as $p) {
                $p = trim($p);
                if (str_contains($p, '/VBO_04')) {
                    $vboSpecies = $p;
                    break;
                }
            }

            if (!$vboSpecies) {
                continue;
            }

            $speciesLabel = $vbo040Labels[$vboSpecies] ?? '';
            if (!$speciesLabel) {
                continue;
            }

            $speciesKey = $this->speciesMap[$speciesLabel]
                ?? str_replace([' breed', ' '], ['', '_'], mb_strtolower($speciesLabel));

            if (in_array($speciesKey, ['canine', 'feline'], true)) {
                continue;
            }

            $breedsBySpecies[$speciesKey][$breedName] = true;
        }

        fclose($handle);

        foreach ($breedsBySpecies as $species => $breeds) {
            $breedsBySpecies[$species] = array_keys($breeds);
            sort($breedsBySpecies[$species]);
        }

        ksort($breedsBySpecies);

        $outputPath = Storage::path('vbo/vbo_breeds.json');
        Storage::makeDirectory('vbo');
        file_put_contents($outputPath, json_encode($breedsBySpecies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $total = 0;
        foreach ($breedsBySpecies as $species => $breeds) {
            $count = count($breeds);
            $total += $count;
            $this->line(sprintf('  %-15s: %d breeds', $species, $count));
        }
        $this->info("Total: {$total} breeds across " . count($breedsBySpecies) . ' species.');
        $this->info("Written to: {$outputPath}");

        $this->warn('Remember to run php artisan db:import-vbo-breeds to import into the database.');

        return Command::SUCCESS;
    }
}
