<?php

namespace Tests\Feature\Commands;

use App\Models\BankAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportBankStatementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_import_ofx()
    {
        $account = BankAccount::factory()->create();
        $file = UploadedFile::fake()->create('extrato.ofx', 100);

        $this->artisan('bank:import-ofx', [
            'bank_account_id' => $account->id,
            'file' => $file->getRealPath(),
        ])->assertSuccessful();
    }

    public function test_import_invalid_account()
    {
        $file = UploadedFile::fake()->create('extrato.ofx', 100);

        $this->artisan('bank:import-ofx', [
            'bank_account_id' => 9999,
            'file' => $file->getRealPath(),
        ])->assertFailed();
    }
}
