<?php

namespace App\Http\Controllers;

use App\Models\NfseConfig;
use Illuminate\Http\Request;

class NfseConfigController extends Controller
{
    public function edit()
    {
        $config = NfseConfig::where('branch_id', auth()->user()->branch_id)->firstOrNew();
        return view('nfse.config', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cnpj' => 'required|string|max:18',
            'municipio_ibge' => 'required|string|max:7',
            'regime_tributario' => 'required|in:mei,simples_nacional,lucro_presumido',
            'serie' => 'required|string|max:3',
            'ambiente' => 'required|in:homologacao,producao',
            'webmania_app_id' => 'required|string',
            'webmania_app_secret' => 'required|string',
            'webmania_consumer_key' => 'required|string',
            'webmania_consumer_secret' => 'required|string',
        ]);

        $branchId = auth()->user()->branch_id;

        NfseConfig::updateOrCreate(
            ['branch_id' => $branchId],
            $validated + ['is_active' => true],
        );

        return redirect()->route('nfse.config')->with('success', 'Configuração NFS-e salva!');
    }
}
