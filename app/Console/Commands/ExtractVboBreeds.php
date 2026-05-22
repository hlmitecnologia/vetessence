<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

    private array $labelToSpecies = [
        'Horse' => 'equine', 'Pig' => 'swine', 'Cattle' => 'bovine',
        'Sheep' => 'ovine', 'Goat' => 'caprine', 'Chicken' => 'avian',
        'Duck (domestic)' => 'avian', 'Goose (domestic)' => 'avian',
        'Turkey' => 'avian', 'Guinea fowl' => 'avian', 'Pigeon' => 'avian',
        'Quail' => 'avian', 'Muscovy duck' => 'avian', 'Ostrich' => 'avian',
        'Partridge' => 'avian', 'Pheasant' => 'avian', 'Emu' => 'avian',
        'Nandu' => 'avian', 'Cassowary' => 'avian', 'Peacock' => 'avian',
        'Swallow' => 'avian', 'Rabbit' => 'lagomorph', 'Guinea pig' => 'rodent',
        'Golden hamster' => 'rodent', 'Fish' => 'fish', 'Goldfish' => 'fish',
        'Rainbow trout' => 'fish', 'Zebrafish' => 'fish', 'Japanese rice fish' => 'fish',
        'Amphibian' => 'amphibian', 'Frog' => 'amphibian',
        'Camel' => 'camelid', 'Dromedary' => 'camelid',
        'Bactrian camel' => 'camelid', 'Alpaca' => 'camelid',
        'Llama' => 'camelid', 'Vicuña' => 'camelid', 'Guanaco' => 'camelid',
        'Deer' => 'cervid', 'Ass' => 'asinine', 'Buffalo' => 'bovine',
        'Yak (domestic)' => 'bovine', 'American Bison' => 'bovine',
        'Domestic yak' => 'bovine', 'Dog' => 'canine', 'Cat' => 'feline',
        'North American deer mouse' => 'rodent', 'Bighorn sheep' => 'ovine',
        'Western clawed frog' => 'amphibian', 'Zebra finch' => 'avian',
        'Dromedary Bactrian Camel' => 'camelid',
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
            $body = $response->body();
            if (substr($body, 0, 2) === "\x1f\x8b") {
                $body = gzdecode($body);
                if ($body === false) {
                    $this->error('Failed to decompress gzip response.');
                    return Command::FAILURE;
                }
            }
            file_put_contents($csvPath, $body);
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

        $entryParents = [];
        $entryLabels = [];
        $vbo040Ids = [];
        $breedCandidates = [];

        rewind($handle);
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $cid = $row[0] ?? '';
            $label = $row[$labelIdx] ?? '';
            $parentsStr = $row[$parentsIdx] ?? '';

            $entryLabels[$cid] = $label;

            $parents = [];
            foreach (explode('|', $parentsStr) as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $parents[] = $p;
                }
            }
            $entryParents[$cid] = $parents;

            if (str_contains($cid, '/VBO_04')) {
                $vbo040Ids[$cid] = true;
            }

            $pattern = '/^(.+?)\s+\((.+)\)$/';
            if (preg_match($pattern, $label, $m)) {
                $breedCandidates[] = [
                    'id' => $cid,
                    'name' => trim($m[1]),
                    'speciesLabel' => $m[2],
                ];
            }
        }

        $this->info('Building parent chain and classifying breeds...');

        $findAllVbo040Ancestors = function (array $parentIds) use (&$entryParents, &$vbo040Ids): array {
            $found = [];
            $seen = [];
            $queue = $parentIds;

            while (!empty($queue)) {
                $current = array_shift($queue);
                if (isset($seen[$current])) continue;
                $seen[$current] = true;

                if (isset($vbo040Ids[$current])) {
                    $found[] = $current;
                    continue;
                }

                $parentsOfCurrent = $entryParents[$current] ?? [];
                foreach ($parentsOfCurrent as $p) {
                    if (!isset($seen[$p])) {
                        $queue[] = $p;
                    }
                }
            }

            return array_unique($found);
        };

        $breedsBySpecies = [];
        $processed = 0;

        foreach ($breedCandidates as $candidate) {
            $rawName = $candidate['name'];
            $speciesLabel = $candidate['speciesLabel'];
            $id = $candidate['id'];

            $cleanName = $rawName;
            $commaPos = mb_strpos($cleanName, ',');
            if ($commaPos !== false) {
                $cleanName = trim(mb_substr($cleanName, 0, $commaPos));
            }

            $lower = mb_strtolower($cleanName);
            if (in_array($lower, ['breed', 'standard', 'crossbreed', 'mixed', 'unknown', 'feral', ''], true) || mb_strlen($cleanName) < 2) {
                continue;
            }

            $parentIds = $entryParents[$id] ?? [];
            $vboIds = $findAllVbo040Ancestors($parentIds);

            $speciesKey = null;

            foreach ($vboIds as $vboId) {
                $vboLabel = $entryLabels[$vboId] ?? '';
                if (!$vboLabel) continue;
                $mappedKey = $this->speciesMap[$vboLabel] ?? null;
                if ($mappedKey && !in_array($mappedKey, ['exotic'], true)) {
                    $speciesKey = $mappedKey;
                    break;
                }
            }

            if (!$speciesKey || $speciesKey === 'exotic') {
                foreach ($vboIds as $vboId) {
                    $vboLabel = $entryLabels[$vboId] ?? '';
                    if (!$vboLabel) continue;
                    $speciesKey = $this->speciesMap[$vboLabel]
                        ?? str_replace([' breed', ' '], ['', '_'], mb_strtolower($vboLabel));
                    if ($speciesKey) break;
                }
            }

            if (!$speciesKey || $speciesKey === 'exotic') {
                $speciesKey = $this->labelToSpecies[$speciesLabel] ?? $speciesKey;
            }

            if (!$speciesKey) {
                continue;
            }

            if (in_array($speciesKey, ['canine', 'feline'], true)) {
                continue;
            }

            $breedsBySpecies[$speciesKey][$cleanName] = true;
            $processed++;
        }

        fclose($handle);

        foreach ($breedsBySpecies as $species => $breeds) {
            $breedsBySpecies[$species] = array_keys($breeds);
            sort($breedsBySpecies[$species]);
        }

        ksort($breedsBySpecies);

        $outputPath = database_path('data/vbo_breeds.json');
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
