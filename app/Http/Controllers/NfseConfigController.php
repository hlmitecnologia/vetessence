<?php

namespace App\Http\Controllers;

use App\Models\NfseConfig;
use Illuminate\Http\Request;

class NfseConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:nfse-config.edit');
    }

    public function edit()
    {
        $config = NfseConfig::firstOrNew();

        return view('nfse.config', compact('config'));
    }

    public function update(Request $request)
    {
        $rules = [
            'provider' => 'required|in:webmania,focusnfe,spedy,tecnospeed,nfeio',
            'ambiente' => 'required|in:homologacao,producao',
        ];

        $provider = $request->input('provider', 'webmania');

        $providerRules = match ($provider) {
            'webmania' => [
                'webmania_app_id' => 'required|string',
                'webmania_app_secret' => 'required|string',
                'webmania_consumer_key' => 'required|string',
                'webmania_consumer_secret' => 'required|string',
            ],
            'focusnfe' => [
                'focusnfe_token' => 'required|string',
            ],
            'spedy' => [
                'spedy_api_key' => 'required|string',
                'spedy_api_secret' => 'required|string',
            ],
            'tecnospeed' => [
                'tecnospeed_token' => 'required|string',
            ],
            'nfeio' => [
                'nfeio_api_key' => 'required|string',
            ],
            default => [],
        };

        $validated = $request->validate(array_merge($rules, $providerRules));

        NfseConfig::updateOrCreate(
            ['id' => NfseConfig::first()?->id],
            $validated + ['is_active' => true],
        );

        return redirect()
            ->route('nfse.config')
            ->with('success', 'Configuração NFS-e salva!');
    }
}
