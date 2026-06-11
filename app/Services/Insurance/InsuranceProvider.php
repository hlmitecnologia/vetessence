<?php

namespace App\Services\Insurance;

use App\Models\ConvenioClaim;

interface InsuranceProvider
{
    public function submitClaim(ConvenioClaim $claim): InsuranceClaimResult;
    public function checkStatus(string $claimId): InsuranceClaimResult;
    public function getName(): string;
}
