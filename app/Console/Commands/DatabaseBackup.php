<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'backup:database {--compress}';
    protected $description = 'Create a MySQL database dump';

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';

        $db = config('database.connections.mysql');
        $host = $db['host'];
        $port = $db['port'];
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $command = "mysqldump -h {$host} -P {$port} -u {$username} -p'{$password}' {$database}";

        if ($this->option('compress')) {
            $filename .= '.gz';
            $command .= ' | gzip';
        }

        $command .= ' > ' . storage_path("app/backups/{$filename}");

        $exitCode = 0;
        $output = null;
        exec($command, $output, $exitCode);

        if ($exitCode === 0) {
            $this->info("Backup created: {$filename}");
        } else {
            $this->error('Backup failed.');
        }

        return $exitCode;
    }
}
