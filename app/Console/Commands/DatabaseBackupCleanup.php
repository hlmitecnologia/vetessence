<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupCleanup extends Command
{
    protected $signature = 'backup:cleanup {--keep=30}';
    protected $description = 'Remove database backups older than specified days';

    public function handle()
    {
        $keep = $this->option('keep');
        $cutoff = now()->subDays($keep);

        $files = Storage::files('backups');
        $deleted = 0;

        foreach ($files as $file) {
            $timestamp = Storage::lastModified($file);
            if ($timestamp < $cutoff->timestamp) {
                Storage::delete($file);
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} old backup files.");
    }
}
