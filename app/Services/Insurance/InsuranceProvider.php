<?php

namespace App\Services\Insurance;

use App\Models\ConvenioClaim;

interface InsuranceProvider
{
    public function submit(ConvenioClaim $claim): bool;
    public function checkStatus(string $externalId): string;
    public function getName(): string;
}
