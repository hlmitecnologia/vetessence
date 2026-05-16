<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:system-update');
    }

    public function index()
    {
        $token = Setting::get('github_token');
        $repo = Setting::get('github_repo', 'hectordufau/vetessence');
        $branch = Setting::get('github_branch', 'main');

        $currentHash = trim(`git log --oneline -1 2>/dev/null`) ?: 'desconhecido';
        $remoteHash = null;
        $behind = null;

        if ($token) {
            $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
            $behind = $this->countBehind($repo, $branch, $token);
        }

        $logs = cache()->get('update_logs', []);

        return view('system-update.index', compact(
            'token', 'repo', 'branch', 'currentHash', 'remoteHash', 'behind', 'logs'
        ));
    }

    public function token(Request $request)
    {
        $data = $request->validate([
            'github_token' => 'required|string',
            'github_repo' => 'required|string|max:200',
            'github_branch' => 'required|string|max:100',
        ]);

        Setting::set('github_token', $data['github_token']);
        Setting::set('github_repo', $data['github_repo']);
        Setting::set('github_branch', $data['github_branch']);

        return redirect()->route('system-update.index')->with('success', 'Token salvo.');
    }

    public function check()
    {
        $repo = Setting::get('github_repo', 'hectordufau/vetessence');
        $branch = Setting::get('github_branch', 'main');
        $token = Setting::get('github_token');

        if (!$token) {
            return redirect()->route('system-update.index')->withErrors(['token' => 'Token nao configurado.']);
        }

        $remoteHash = $this->fetchRemoteHash($repo, $branch, $token);
        $behind = $this->countBehind($repo, $branch, $token);

        return redirect()->route('system-update.index')->with('remote_hash', $remoteHash)->with('behind', $behind);
    }

    public function apply()
    {
        $token = Setting::get('github_token');
        $repo = Setting::get('github_repo', 'hectordufau/vetessence');
        $branch = Setting::get('github_branch', 'main');

        if (!$token) {
            return redirect()->route('system-update.index')->withErrors(['token' => 'Token nao configurado.']);
        }

        $log = [];
        $log[] = '[' . now() . '] Iniciando atualizacao...';

        try {
            // maintenance down
            $log[] = exec('php artisan down 2>&1');
            $log[] = exec('php artisan route:clear 2>&1');
            $log[] = exec('php artisan config:clear 2>&1');

            // git pull
            $url = "https://{$token}@github.com/{$repo}.git";
            $log[] = exec("git pull {$url} {$branch} 2>&1", $out, $exitCode);

            if ($exitCode === 0) {
                // migrations
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

        // maintenance up
        $log[] = exec('php artisan up 2>&1');
        $log[] = '[' . now() . '] Finalizado.';

        // store log
        $logs = cache()->get('update_logs', []);
        array_unshift($logs, implode("\n", $log));
        cache()->forever('update_logs', array_slice($logs, 0, 20));

        return redirect()->route('system-update.index')->with('success', 'Atualizacao concluida.');
    }

    public function history()
    {
        $logs = cache()->get('update_logs', []);
        return view('system-update.history', compact('logs'));
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
