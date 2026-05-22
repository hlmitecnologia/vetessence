<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbImportGeo extends Command
{
    protected $signature = 'db:import-geo';
    protected $description = 'Import states and cities from megapedigreedb';

    public function handle(): int
    {
        if (!DB::connection('megapedigreedb')->getDatabaseName()) {
            $this->error('megapedigreedb connection not available.');
            return Command::FAILURE;
        }

        $this->importStates();
        $this->importCities();
        $this->migrateExistingData();

        $this->info('Geographic data imported successfully!');
        return Command::SUCCESS;
    }

    protected function importStates(): void
    {
        $this->info('Importing states...');

        $rows = DB::connection('megapedigreedb')
            ->table('ufs')
            ->join('states', 'ufs.idstate', '=', 'states.id')
            ->where('states.country_code', 'BR')
            ->select('states.name', 'ufs.siglauf as uf', 'states.id as remote_state_id')
            ->orderBy('states.name')
            ->get();

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        foreach ($rows as $row) {
            State::updateOrCreate(
                ['uf' => $row->uf],
                ['name' => $row->name, 'country' => 'BR']
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($rows->count() . ' states imported.');
    }

    protected function importCities(): void
    {
        $this->info('Importing cities...');

        $rows = DB::connection('megapedigreedb')
            ->table('cities')
            ->where('country_code', 'BR')
            ->select('id as remote_city_id', 'name', 'state_id as remote_state_id')
            ->orderBy('name')
            ->get();

        $stateMap = [];
        $states = State::pluck('id', 'uf');
        $ufMap = DB::connection('megapedigreedb')
            ->table('ufs')
            ->pluck('siglauf', 'idstate');

        foreach ($ufMap as $stateId => $uf) {
            if (isset($states[$uf])) {
                $stateMap[$stateId] = $states[$uf];
            }
        }

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $inserted = 0;
        foreach ($rows as $row) {
            $localStateId = $stateMap[$row->remote_state_id] ?? null;
            if (!$localStateId) {
                continue;
            }

            City::updateOrCreate(
                [
                    'state_id' => $localStateId,
                    'name' => $row->name,
                ],
                ['state_id' => $localStateId, 'name' => $row->name]
            );
            $inserted++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($inserted . ' cities imported.');
    }

    protected function migrateExistingData(): void
    {
        $this->info('Migrating existing text city/state references to FK...');

        $stateMap = State::pluck('id', 'uf');

        foreach (['tutors', 'suppliers', 'branches'] as $table) {
            $records = DB::table($table)
                ->whereNotNull('state')
                ->where('state', '!=', '')
                ->get(['id', 'state', 'city']);

            foreach ($records as $record) {
                $stateId = $stateMap[strtoupper(trim($record->state))] ?? null;
                if (!$stateId) {
                    continue;
                }

                $update = ['state_id' => $stateId];

                if (!empty($record->city)) {
                    $city = City::where('state_id', $stateId)
                        ->where('name', trim($record->city))
                        ->first();
                    if ($city) {
                        $update['city_id'] = $city->id;
                    }
                }

                DB::table($table)->where('id', $record->id)->update($update);
            }
        }
    }
}
