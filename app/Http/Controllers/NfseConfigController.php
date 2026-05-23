<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\NfseConfig;
use Illuminate\Http\Request;

class NfseConfigController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        if ($user->can('unidades')) {
            $branches = Branch::active()->orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        $branchId = $request->query('branch_id', $user->branch_id);

        $config = NfseConfig::where('branch_id', $branchId)->firstOrNew();

        return view('nfse.config', compact('branches', 'branchId', 'config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
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

        NfseConfig::updateOrCreate(
            ['branch_id' => $validated['branch_id']],
            $validated + ['is_active' => true],
        );

        return redirect()
            ->route('nfse.config', ['branch_id' => $validated['branch_id']])
            ->with('success', 'Configuração NFS-e salva!');
    }
}
