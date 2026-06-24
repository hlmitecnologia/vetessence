<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SystemUpdateController extends Controller
{
    const RATE_LIMIT_KEY = 'update_throttled_';
    const RATE_LIMIT_MINUTES = 30;

    public function __construct()
    {
        $this->middleware('can:system-update');
    }

    public function index()
    {
        $token = $this->getDecryptedToken();
        $repo = $this->getRepo();
        $branch = $this->getBranch();

        $currentHash = $this->getCurrentHash();
        $remoteHash = null;
        $behind = null;

        if ($token) {
            $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
            $behind = $this->countBehind($repo, $branch, $token);
        }

        $hasToken = (bool) ($this->getDecryptedToken());
        $hasRepo = (bool) ($this->getRepo());
        $hasBranch = (bool) ($this->getBranch());

        $licenseKey = Setting::getEncrypted('license_key');

        $logs = cache()->get('update_logs', []);

        $throttledUntil = Cache::get(self::RATE_LIMIT_KEY . auth()->id());

        return view('system-update.index', compact(
            'hasToken', 'hasRepo', 'hasBranch',
            'currentHash', 'remoteHash', 'behind', 'logs',
            'licenseKey', 'repo', 'branch', 'throttledUntil'
        ));
    }

    public function token(Request $request)
    {
        $data = $request->validate([
            'github_token' => 'nullable|string',
            'github_repo' => 'nullable|string|max:200',
            'github_branch' => 'nullable|string|max:100',
        ]);

        if (!empty($data['github_token'])) {
            Setting::setEncrypted('github_token', $data['github_token']);
        }

        if (!empty($data['github_repo'])) {
            Setting::set('github_repo', $data['github_repo']);
        }

        if (!empty($data['github_branch'])) {
            Setting::set('github_branch', $data['github_branch']);
        }

        return redirect()->route('system-update.index')->with('success', 'Configuração salva.');
    }

    public function check()
    {
        $token = $this->getDecryptedToken();
        $repo = $this->getRepo();
        $branch = $this->getBranch();

        if (!$token) {
            return redirect()->route('system-update.index')->with('error', 'Token não configurado.');
        }

        $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
        $behind = $this->countBehind($repo, $branch, $token);

        if ($remoteHash === null) {
            return redirect()->route('system-update.index')->with('error', 'Erro ao consultar GitHub. Verifique token e repositório.');
        }

        return redirect()->route('system-update.index')->with('success', 'Verificação concluída.');
    }

    public function license(Request $request)
    {
        $data = $request->validate([
            'license_key' => 'nullable|string|max:255',
        ]);

        Setting::setEncrypted('license_key', $data['license_key'] ?? '');

        return redirect()->route('system-update.index')->with('success', 'Licença salva.');
    }

    public function apply(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $userId = auth()->id();
        $throttledUntil = Cache::get(self::RATE_LIMIT_KEY . $userId);
        if ($throttledUntil && now()->lt($throttledUntil)) {
            $minutes = now()->diffInMinutes($throttledUntil);
            return redirect()->route('system-update.index')
                ->with('error', "Aguarde {$minutes} minuto(s) antes de atualizar novamente.");
        }

        $token = $this->getDecryptedToken();
        $repo = $this->getRepo();
        $branch = $this->getBranch();

        if (!$token) {
            return redirect()->route('system-update.index')->with('error', 'Token não configurado.');
        }

        $log = [];
        $log[] = '[' . now() . '] Iniciando atualização...';

        // Pre-update backup
        $backupResult = $this->backupDatabase();
        $log[] = $backupResult['message'];

        try {
            $log[] = exec('php artisan down 2>&1');
            $log[] = exec('php artisan route:clear 2>&1');
            $log[] = exec('php artisan config:clear 2>&1');

            $url = "https://{$token}@github.com/{$repo}.git";
            $log[] = exec("git pull {$url} {$branch} 2>&1", $out, $exitCode);

            if ($exitCode === 0) {
                Artisan::call('migrate', ['--force' => true]);
                $log[] = Artisan::output();

                $log[] = exec('php artisan route:clear 2>&1');
                $log[] = exec('php artisan config:clear 2>&1');
                $log[] = exec('php artisan view:clear 2>&1');
            } else {
                $log[] = 'ERRO: git pull falhou (merge conflict?).';
            }
        } catch (\Exception $e) {
            $log[] = 'ERRO: ' . $e->getMessage();
        }

        $log[] = exec('php artisan up 2>&1');
        $log[] = '[' . now() . '] Finalizado.';

        $logs = cache()->get('update_logs', []);
        array_unshift($logs, implode("\n", $log));
        cache()->forever('update_logs', array_slice($logs, 0, 20));

        // Rate limit: 30 min between updates per user
        Cache::put(self::RATE_LIMIT_KEY . $userId, now()->addMinutes(self::RATE_LIMIT_MINUTES), now()->addMinutes(self::RATE_LIMIT_MINUTES));

        return redirect()->route('system-update.index')->with('success', 'Atualização concluída.');
    }

    public function history()
    {
        $logs = cache()->get('update_logs', []);
        return view('system-update.history', compact('logs'));
    }

    private function getDecryptedToken(): ?string
    {
        $dbToken = Setting::getEncrypted('github_token');
        if ($dbToken) {
            return $dbToken;
        }

        // Fallback: existing plaintext token (migração suave)
        $plain = Setting::get('github_token');
        if ($plain) {
            Setting::setEncrypted('github_token', $plain);
            return $plain;
        }

        return config('update.token');
    }

    private function getRepo(): string
    {
        return Setting::get('github_repo') ?? config('update.repo');
    }

    private function getBranch(): string
    {
        return Setting::get('github_branch') ?? config('update.branch');
    }

    private function getCurrentHash(): string
    {
        $hash = $this->readGitHead();
        return $hash ? substr($hash, 0, 7) : 'desconhecido';
    }

    private function getFullCurrentHash(): ?string
    {
        return $this->readGitHead();
    }

    private function readGitHead(): ?string
    {
        $headFile = base_path('.git/HEAD');
        $head = @file_get_contents($headFile);
        if (!$head) {
            return null;
        }

        // Detached HEAD: arquivo contém o hash direto
        if (preg_match('/^[a-f0-9]{40}$/i', trim($head))) {
            return trim($head);
        }

        // Branch: ref: refs/heads/main
        if (preg_match('/^ref: (.+)$/m', $head, $m)) {
            $refPath = base_path('.git/' . trim($m[1]));
            $hash = @file_get_contents($refPath);
            if ($hash && preg_match('/^[a-f0-9]{40}$/i', trim($hash))) {
                return trim($hash);
            }
        }

        return null;
    }

    private function fetchRemoteHash(string $repo, string $branch, string $token): ?string
    {
        $url = "https://api.github.com/repos/{$repo}/branches/{$branch}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                'User-Agent: VetEssence',
                'Accept: application/vnd.github+json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::warning('GitHub API (branches) falhou', [
                'url' => $effectiveUrl,
                'repo' => $repo,
                'branch' => $branch,
                'http_code' => $httpCode,
                'error' => $error,
                'response' => mb_substr($response, 0, 500),
            ]);
            return null;
        }

        $data = json_decode($response, true);
        return $data['commit']['sha'] ?? null;
    }

    private function countBehind(string $repo, string $branch, string $token): ?int
    {
        $current = $this->getFullCurrentHash();
        if (!$current) return null;

        $url = "https://api.github.com/repos/{$repo}/compare/{$current}...{$branch}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                'User-Agent: VetEssence',
                'Accept: application/vnd.github+json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::warning('GitHub API (compare) falhou', [
                'repo' => $repo,
                'branch' => $branch,
                'http_code' => $httpCode,
                'error' => $error,
                'response' => mb_substr($response, 0, 500),
            ]);
            return null;
        }

        $data = json_decode($response, true);
        return $data['behind_by'] ?? null;
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
            return ['success' => true, 'message' => "Backup: {$filename}"];
        }

        return ['success' => false, 'message' => 'Backup falhou: ' . $process->getErrorOutput()];
    }
}
