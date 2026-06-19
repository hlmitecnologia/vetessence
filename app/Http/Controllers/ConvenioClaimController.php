<?php

namespace App\Http\Controllers;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ConvenioClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:convenio-claims.view')->only(['index', 'show']);
        $this->middleware('can:convenio-claims.create')->only(['create', 'store']);
        $this->middleware('can:convenio-claims.edit')->only(['edit', 'update']);
        $this->middleware('can:convenio-claims.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = ConvenioClaim::with(['convenioPet.convenio', 'convenioPet.pet', 'invoice']);
        if ($request->status) $query->where('status', $request->status);
        $claims = $query->latest()->paginate(20);
        return view('convenio-claims.index', compact('claims'));
    }

    public function create()
    {
        return redirect()->route('convenio-claims.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'convenio_pet_id' => 'required|exists:convenio_pet,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount_requested' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['claim_number'] = 'CLM-' . strtoupper(uniqid());
        $data['status'] = 'draft';
        ConvenioClaim::create($data);
        return redirect()->route('convenio-claims.index')
            ->with('success', 'Solicitação de reembolso criada.');
    }

    public function show(ConvenioClaim $convenioClaim)
    {
        $convenioClaim->load(['convenioPet.convenio', 'convenioPet.pet', 'invoice']);
        return view('convenio-claims.show', compact('convenioClaim'));
    }

    public function edit($convenioClaim)
    {
        return redirect()->route('convenio-claims.index');
    }

    public function update(Request $request, ConvenioClaim $convenioClaim)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,filed,approved,rejected',
            'amount_approved' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($data['status'] === 'filed') $data['filed_at'] = now();
        if ($data['status'] === 'approved' || $data['status'] === 'rejected') $data['response_at'] = now();

        $convenioClaim->update($data);
        return redirect()->route('convenio-claims.index')
            ->with('success', 'Solicitação atualizada.');
    }

    public function destroy(ConvenioClaim $convenioClaim)
    {
        $convenioClaim->delete();
        return redirect()->route('convenio-claims.index')
            ->with('success', 'Solicitação removida.');
    }
}
