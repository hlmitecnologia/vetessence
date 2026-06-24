<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SystemUpdate extends Command
{
    protected $signature = 'system:update
        {--force : Skip confirmation prompt}
        {--no-backup : Skip database backup}
        {--branch= : Override branch for this update}';

    protected $description = 'Apply system update via git pull + migrate with pre-backup';

    public function handle()
    {
        $repo = config('update.repo');
        $branch = $this->option('branch') ?: config('update.branch');
        $token = config('update.token');

        if (!$token) {
            $this->error('GitHub token not configured. Set GITHUB_TOKEN in .env or via /system-update.');
            return 1;
        }

        if (!$this->option('force')) {
            $this->line("Repo:   {$repo}");
            $this->line("Branch: {$branch}");
            if (!$this->confirm('Proceed with update?')) {
                $this->info('Cancelled.');
                return 0;
            }
        }

        $this->info('Starting system update...');
        $log = [];

        // 1. Pre-update backup
        if (!$this->option('no-backup')) {
            $this->line('Creating database backup...');
            $backupResult = $this->backupDatabase();
            $log[] = $backupResult['message'];
            $this->line($backupResult['message']);
        }

        // 2. Maintenance mode
        $this->call('down');
        $log[] = '[system:update] Application down';

        // 3. Route/config cache clear
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        $log[] = '[system:update] Cache cleared';

        // 4. Git pull
        $this->line('Pulling from git...');
        $gitResult = $this->gitPull($repo, $branch, $token);
        $log[] = $gitResult['message'];

        if ($gitResult['success']) {
            // 5. Migrate
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();
            $log[] = $migrateOutput;
            $this->line($migrateOutput);

            // 6. Rebuild caches
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            $log[] = '[system:update] Post-migration cache cleared';
        } else {
            $this->error('Git pull failed (merge conflict?).');
        }

        // 7. Back up
        $this->call('up');
        $log[] = '[system:update] Application up';

        // 8. Log
        $this->saveLog(implode("\n", $log));
        $this->info('System update completed.');
        return $gitResult['success'] ? 0 : 1;
    }

    private function backupDatabase(): array
    {
        $filename = 'pre-update-' . now()->format('Y-m-d_H-i-s') . '.sql';

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

        if ($process->isSuccessful()) {
            file_put_contents($path, $process->getOutput());
            return ['success' => true, 'message' => "Backup created: {$filename}"];
        }

        return ['success' => false, 'message' => 'Backup failed: ' . $process->getErrorOutput()];
    }

    private function gitPull(string $repo, string $branch, string $token): array
    {
        $url = "https://{$token}@github.com/{$repo}.git";
        $process = new Process(['git', 'pull', $url, $branch]);
        $process->setTimeout(120);
        $process->run();

        $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());

        if ($process->isSuccessful()) {
            return ['success' => true, 'message' => $output];
        }

        Log::warning('system:update git pull failed', [
            'repo' => $repo,
            'branch' => $branch,
            'output' => $output,
        ]);

        return ['success' => false, 'message' => "Git pull failed:\n{$output}"];
    }

    private function saveLog(string $logEntry): void
    {
        $logs = Cache::get('update_logs', []);
        array_unshift($logs, $logEntry);
        Cache::forever('update_logs', array_slice($logs, 0, 20));
    }
}
