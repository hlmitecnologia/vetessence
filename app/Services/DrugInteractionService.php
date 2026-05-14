<?php

namespace App\Services;

use App\Models\DrugInteraction;
use Illuminate\Support\Collection;

class DrugInteractionService
{
    /**
     * Check for interactions among a list of drug names.
     *
     * @param  array|Collection  $drugs  List of drug names.
     * @return Collection  Collection of interaction results.
     */
    public function check($drugs): Collection
    {
        $drugs = collect($drugs)->map(fn ($d) => trim(strtolower($d)))->unique()->values();
        $interactions = collect();

        if ($drugs->count() < 2) {
            return $interactions;
        }

        $all = DrugInteraction::active()->get();

        for ($i = 0; $i < $drugs->count(); $i++) {
            for ($j = $i + 1; $j < $drugs->count(); $j++) {
                $a = $drugs[$i];
                $b = $drugs[$j];

                $match = $all->first(function ($interaction) use ($a, $b) {
                    $ia = strtolower($interaction->drug_a);
                    $ib = strtolower($interaction->drug_b);
                    return ($ia === $a && $ib === $b) || ($ia === $b && $ib === $a);
                });

                if ($match) {
                    $interactions->push($match);
                }
            }
        }

        return $interactions;
    }

    /**
     * Get all known interactions for a specific drug.
     */
    public function forDrug(string $drugName): Collection
    {
        return DrugInteraction::active()->forDrug($drugName)->get();
    }

    /**
     * Check if a specific pair has an interaction.
     */
    public function between(string $drugA, string $drugB): ?DrugInteraction
    {
        $a = strtolower($drugA);
        $b = strtolower($drugB);

        return DrugInteraction::active()
            ->where(function ($q) use ($a, $b) {
                $q->where(function ($q2) use ($a, $b) {
                    $q2->where('drug_a', $a)->where('drug_b', $b);
                })->orWhere(function ($q2) use ($a, $b) {
                    $q2->where('drug_a', $b)->where('drug_b', $a);
                });
            })
            ->first();
    }
}
