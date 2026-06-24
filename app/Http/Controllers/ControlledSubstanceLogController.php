<?php

namespace App\Http\Controllers;

use App\Models\ControlledSubstance;
use App\Models\ControlledSubstanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlledSubstanceLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:estoque');
    }

    public function index(Request $request)
    {
        $query = ControlledSubstanceLog::with(['substance', 'user', 'pet']);

        if ($request->substance_id) {
            $query->where('controlled_substance_id', $request->substance_id);
        }

        $substances = ControlledSubstance::orderBy('name')->get();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return view('controlled-substance-logs.index', compact('logs', 'substances'));
    }

    public function create()
    {
        $substances = ControlledSubstance::where('is_active', true)->orderBy('name')->get();
        return view('controlled-substance-logs.create', compact('substances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'controlled_substance_id' => 'required|exists:controlled_substances,id',
            'pet_id' => 'nullable|exists:pets,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'witness_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $substance = ControlledSubstance::findOrFail($validated['controlled_substance_id']);

            $validated['user_id'] = auth()->id();
            $validated['balance_before'] = $substance->current_stock;

            if ($validated['type'] === 'out') {
                if ($substance->current_stock < $validated['quantity']) {
                    return back()->with('error', 'Saldo insuficiente para saída.')->withInput();
                }
                $substance->current_stock -= $validated['quantity'];
            } else {
                $substance->current_stock += $validated['quantity'];
            }

            $validated['balance_after'] = $substance->current_stock;

            ControlledSubstanceLog::create($validated);
            $substance->save();

            DB::commit();
            return redirect()->route('controlled-substance-logs.index', ['substance' => $substance->id])
                ->with('success', 'Movimentação registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar movimentação.')->withInput();
        }
    }

    public function show(ControlledSubstanceLog $controlledSubstanceLog)
    {
        $controlledSubstanceLog->load(['substance', 'user', 'pet', 'prescription', 'witness']);
        return view('controlled-substance-logs.show', compact('controlledSubstanceLog'));
    }

    public function destroy(ControlledSubstanceLog $controlledSubstanceLog)
    {
        DB::beginTransaction();
        try {
            $substance = $controlledSubstanceLog->substance;

            if ($controlledSubstanceLog->type === 'in') {
                $substance->current_stock -= $controlledSubstanceLog->quantity;
            } else {
                $substance->current_stock += $controlledSubstanceLog->quantity;
            }

            if ($substance->current_stock < 0) {
                return back()->with('error', 'Não é possível excluir: saldo ficaria negativo.');
            }

            $substance->save();
            $controlledSubstanceLog->delete();

            DB::commit();
            return redirect()->route('controlled-substance-logs.index', ['substance' => $substance->id])
                ->with('success', 'Movimentação excluída!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir movimentação.');
        }
    }
}
