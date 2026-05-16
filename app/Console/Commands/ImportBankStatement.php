<?php

namespace App\Console\Commands;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Console\Command;

class ImportBankStatement extends Command
{
    protected $signature = 'bank:import-ofx {bank_account_id} {file}';
    protected $description = 'Import OFX/CSV bank statement and create transactions';

    public function handle()
    {
        $accountId = (int) $this->argument('bank_account_id');
        $file = $this->argument('file');

        $account = BankAccount::find($accountId);
        if (!$account) {
            $this->error('Conta bancária não encontrada.');
            return 1;
        }

        if (!file_exists($file)) {
            $this->error('Arquivo não encontrado: ' . $file);
            return 1;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $count = 0;

        foreach ($lines as $line) {
            $parts = str_getcsv($line);
            if (count($parts) < 4) continue;

            $date = $parts[0];
            $description = $parts[1];
            $amount = (float) $parts[2];
            $type = $parts[3] ?? 'credit';

            BankTransaction::create([
                'bank_account_id' => $accountId,
                'description' => $description,
                'amount' => abs($amount),
                'transaction_date' => $date,
                'type' => $type === 'debit' ? 'debit' : 'credit',
                'status' => 'pending',
            ]);
            $count++;
        }

        $this->info("{$count} transações importadas com sucesso.");
        return 0;
    }
}
