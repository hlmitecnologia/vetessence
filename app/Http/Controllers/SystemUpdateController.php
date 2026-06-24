<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SystemUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:system-update');
    }

    public function index()
    {
        $token = $this->getDecryptedToken();
        $repo = $this->getRepo();
        $branch = $this->getBranch();

        $currentHash = trim(`git log --oneline -1 2>/dev/null`) ?: 'desconhecido';
        $remoteHash = null;
        $behind = null;

        if ($token) {
            $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
            $behind = $this->countBehind($repo, $branch, $token);
        }

        $hasToken = (bool) Setting::get('github_token');
        $hasRepo = (bool) Setting::get('github_repo');
        $hasBranch = (bool) Setting::get('github_branch');

        $licenseKey = Setting::getEncrypted('license_key');

        $logs = cache()->get('update_logs', []);

        return view('system-update.index', compact(
            'hasToken', 'hasRepo', 'hasBranch',
            'currentHash', 'remoteHash', 'behind', 'logs',
            'licenseKey', 'repo', 'branch'
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
            return redirect()->route('system-update.index')->withErrors(['token' => 'Token não configurado.']);
        }

        $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
        $behind = $this->countBehind($repo, $branch, $token);

        return redirect()->route('system-update.index')->with('remote_hash', $remoteHash)->with('behind', $behind);
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

        $token = $this->getDecryptedToken();
        $repo = $this->getRepo();
        $branch = $this->getBranch();

        if (!$token) {
            return redirect()->route('system-update.index')->withErrors(['token' => 'Token não configurado.']);
        }

        $log = [];
        $log[] = '[' . now() . '] Iniciando atualização...';

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

    private function fetchRemoteHash(string $repo, string $branch, string $token): ?string
    {
        $url = "https://api.github.com/repos/{$repo}/branches/{$branch}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: token {$token}",
                'User-Agent: VetEssence',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) return null;

        $data = json_decode($response, true);
        return $data['commit']['sha'] ?? null;
    }

    private function countBehind(string $repo, string $branch, string $token): ?int
    {
        $current = trim(`git rev-parse HEAD 2>/dev/null`);
        if (!$current) return null;

        $url = "https://api.github.com/repos/{$repo}/compare/{$current}...{$branch}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: token {$token}",
                'User-Agent: VetEssence',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) return null;

        $data = json_decode($response, true);
        return $data['behind_by'] ?? null;
    }
}
