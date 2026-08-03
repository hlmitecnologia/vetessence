<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class VersionBump extends Command
{
    protected $signature = 'version:bump
        {increment=patch : major, minor, patch ou versão explícita (ex: 1.2.3)}
        {--dry-run : Exibe as alterações sem aplicar}
        {--no-push : Commita e tageia localmente, sem push nem Release}';

    protected $description = 'Incrementa a versão e atualiza VERSION, package.json, README, changelog, git tag e GitHub Release';

    public function handle(): int
    {
        $current = $this->readCurrentVersion();
        $new = $this->parseIncrement($current, $this->argument('increment'));

        $this->info("Versão atual: {$current}");
        $this->info("Nova versão:  {$new}");
        $this->newLine();

        $plan = $this->buildPlan($new);

        $rows = array_map(fn ($item) => [$item['file'], $item['action']], $plan);
        $this->table(['Arquivo', 'Ação'], $rows);

        if ($this->option('dry-run')) {
            $this->warn('DRY-RUN: nenhuma alteração foi aplicada.');
            return 0;
        }

        $this->info('Aplicando alterações...');
        foreach ($plan as $item) {
            $this->line('  ' . $item['action'] . ' → ' . $item['file']);
            call_user_func($item['apply']);
        }

        Artisan::call('docs:publish');

        $this->gitCommitAndTag($new);

        if ($this->option('no-push')) {
            $this->info('--no-push: commit e tag criados localmente. Push e Release ficam pendentes.');
            return 0;
        }

        $this->gitPushAndRelease($new);

        $this->newLine();
        $this->info("Versão v{$new} publicada com sucesso.");
        return 0;
    }

    private function readCurrentVersion(): string
    {
        $version = trim((string) @file_get_contents(base_path('VERSION')));
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            $this->error("Arquivo VERSION inválido: '{$version}'. Esperado X.Y.Z.");
            exit(1);
        }
        return $version;
    }

    private function parseIncrement(string $current, string $increment): string
    {
        if (preg_match('/^\d+\.\d+\.\d+$/', $increment)) {
            if (version_compare($increment, $current, '<=')) {
                $this->error("Versão '{$increment}' deve ser maior que a atual ({$current}).");
                exit(1);
            }
            return $increment;
        }

        [$major, $minor, $patch] = array_map('intval', explode('.', $current));

        return match ($increment) {
            'major' => sprintf('%d.0.0', $major + 1),
            'minor' => sprintf('%d.%d.0', $major, $minor + 1),
            'patch' => sprintf('%d.%d.%d', $major, $minor, $patch + 1),
            default => $this->error("Incremento inválido '{$increment}'. Use major, minor, patch ou X.Y.Z.") ?: exit(1),
        };
    }

    private function buildPlan(string $new): array
    {
        $today = now()->format('Y-m-d');
        $repo = config('update.repo');

        $plan = [];

        // 1. VERSION
        $plan[] = [
            'file' => 'VERSION',
            'action' => "Atualizar para {$new}",
            'apply' => fn () => File::put(base_path('VERSION'), $new . PHP_EOL),
        ];

        // 2. package.json
        $plan[] = [
            'file' => 'package.json',
            'action' => "Atualizar \"version\" para {$new}",
            'apply' => function () use ($new) {
                $path = base_path('package.json');
                $content = File::get($path);
                File::put($path, preg_replace('/"version":\s*"[^"]+"/', '"version": "' . $new . '"', $content, 1));
            },
        ];

        // 3. README badge
        $plan[] = [
            'file' => 'README.md',
            'action' => "Atualizar badge de versão para v{$new}",
            'apply' => function () use ($new) {
                $path = base_path('README.md');
                $content = File::get($path);
                if (preg_match('/versão-v\d+\.\d+\.\d+/', $content)) {
                    $content = preg_replace('/versão-v\d+\.\d+\.\d+/', "versão-v{$new}", $content, 1);
                } else {
                    $badge = "[![Versão](https://img.shields.io/badge/versão-v{$new}-blue.svg)](https://github.com/{$this->repoForBadge()}/releases)";
                    $content = preg_replace('/(\[!\[Testes\][^\n]+)/', "$1\n" . $badge, $content, 1);
                }
                File::put($path, $content);
            },
        ];

        // 4. Changelog
        $changelogPath = resource_path('docs/changelog/index.md');
        $plan[] = [
            'file' => 'resources/docs/changelog/index.md',
            'action' => "Finalizar seção [Não versionado] como v{$new}",
            'apply' => function () use ($changelogPath, $new, $today) {
                File::put($changelogPath, $this->rewriteChangelog(File::get($changelogPath), $new, $today));
            },
        ];

        return $plan;
    }

    private function repoForBadge(): string
    {
        $repo = config('update.repo');
        if ($repo) {
            return $repo;
        }
        $remote = $this->runGit(['remote', 'get-url', 'origin']);
        if (preg_match('#[:/]([^/]+/[^/]+)\.git$#', trim($remote), $m)) {
            return $m[1];
        }
        return 'hlmitecnologia/vetessence';
    }

    private function rewriteChangelog(string $content, string $new, string $today): string
    {
        $lines = preg_split('/\R/', $content);
        $firstHeader = null;
        foreach ($lines as $i => $line) {
            if (str_starts_with($line, '## [')) {
                $firstHeader = $i;
                break;
            }
        }

        if ($firstHeader === null) {
            return $content;
        }

        if (!str_contains($lines[$firstHeader], 'Não versionado')) {
            return $content;
        }

        $nextHeader = null;
        for ($i = $firstHeader + 1; $i < count($lines); $i++) {
            if (str_starts_with($lines[$i], '## [')) {
                $nextHeader = $i;
                break;
            }
        }

        $sectionLines = array_slice($lines, $firstHeader + 1, $nextHeader === null ? null : $nextHeader - $firstHeader - 1);
        while ($sectionLines && trim(end($sectionLines)) === '') {
            array_pop($sectionLines);
        }

        $head = array_slice($lines, 0, $firstHeader);
        while ($head && trim(end($head)) === '') {
            array_pop($head);
        }
        $tail = $nextHeader === null ? [] : array_slice($lines, $nextHeader);

        return implode("\n", array_merge(
            $head,
            ['', "## [Não versionado] — {$today}", ''],
            ["## [v{$new}] — {$today}"],
            $sectionLines,
            $tail,
        ));
    }

    private function gitCommitAndTag(string $new): void
    {
        $this->runGit(['add', '-A']);
        $commit = $this->runGit(['commit', '-m', "release: v{$new}"]);
        $this->line('  ' . trim($commit));

        $tag = $this->runGit(['tag', "v{$new}"]);
        $this->line('  Tag v' . $new . ' criada.');
    }

    private function gitPushAndRelease(string $new): void
    {
        $branch = config('update.branch') ?: 'main';

        $push = $this->runGit(['push', 'origin', $branch]);
        $this->line('  ' . trim($push));

        $tag = $this->runGit(['push', 'origin', "v{$new}"]);
        $this->line('  ' . trim($tag));

        $this->createGithubRelease($new);
    }

    private function createGithubRelease(string $new): void
    {
        $repo = config('update.repo');
        $token = config('update.token') ?: $this->extractTokenFromRemote();

        if (!$repo || !$token) {
            $this->warn('  Repositório/token não disponíveis — Release do GitHub não criado.');
            return;
        }

        $body = $this->extractReleaseNotes($new);
        if ($body === '') {
            $body = "Versão v{$new} do VetEssence.";
        }

        $payload = json_encode([
            'tag_name' => "v{$new}",
            'name' => "v{$new}",
            'body' => $body,
            'draft' => false,
            'prerelease' => false,
        ]);

        $process = new Process([
            'curl', '-sS', '-X', 'POST',
            "https://api.github.com/repos/{$repo}/releases",
            '-H', 'Authorization: Bearer ' . $token,
            '-H', 'Accept: application/vnd.github+json',
            '-H', 'User-Agent: VetEssence',
            '-d', $payload,
        ]);
        $process->setTimeout(60);
        $process->run();

        if ($process->isSuccessful()) {
            $response = json_decode($process->getOutput(), true);
            $this->info('  Release GitHub criado: ' . ($response['html_url'] ?? 'v' . $new));
        } else {
            $this->warn('  Falha ao criar Release: ' . substr($process->getErrorOutput() ?: $process->getOutput(), 0, 300));
        }
    }

    private function extractReleaseNotes(string $new): string
    {
        $content = File::get(resource_path('docs/changelog/index.md'));
        $lines = preg_split('/\R/', $content);

        $start = null;
        foreach ($lines as $i => $line) {
            if (str_starts_with($line, '## [')) {
                if (str_contains($line, "v{$new}")) {
                    $start = $i;
                    break;
                }
            }
        }

        if ($start === null) {
            return '';
        }

        $end = count($lines);
        for ($i = $start + 1; $i < count($lines); $i++) {
            if (str_starts_with($lines[$i], '## [')) {
                $end = $i;
                break;
            }
        }

        return implode("\n", array_slice($lines, $start, $end - $start));
    }

    private function extractTokenFromRemote(): ?string
    {
        $remote = $this->runGit(['remote', 'get-url', 'origin']);
        if (preg_match('#https?://[^:]+:([^@]+)@#', $remote, $m)) {
            return $m[1];
        }
        return null;
    }

    private function runGit(array $args): string
    {
        $process = new Process(array_merge(['git'], $args));
        $process->setTimeout(120);
        $process->run();

        return trim($process->getOutput() . "\n" . $process->getErrorOutput());
    }
}
