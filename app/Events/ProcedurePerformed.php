<?php

namespace App\Events;

use App\Models\Vaccination;
use Illuminate\Foundation\Events\Dispatchable;

class ProcedurePerformed
{
    use Dispatchable;

    public $vaccination;

    public function __construct(Vaccination $vaccination)
    {
        $this->vaccination = $vaccination;
    }
}
