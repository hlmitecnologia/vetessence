<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

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

        $path = storage_path("app/backups/{$filename}");

        $process = new Process([
            'mysqldump',
            '-h', $host,
            '-P', $port,
            '-u', $username,
            '-p' . $password,
            $database,
        ]);
        $process->setTimeout(120);
        $process->run();

        if ($this->option('compress')) {
            $filename .= '.gz';
            $gz = gzopen($path . '.gz', 'w');
            gzwrite($gz, $process->getOutput());
            gzclose($gz);
        } else {
            file_put_contents($path, $process->getOutput());
        }

        if ($process->isSuccessful()) {
            $this->info("Backup criado: {$filename}");
        } else {
            $this->error('Falha no backup: ' . $process->getErrorOutput());

            if (! $this->option('compress')) {
                @unlink($path);
            } else {
                @unlink($path . '.gz');
            }
        }

        return $process->isSuccessful() ? 0 : 1;
    }
}
