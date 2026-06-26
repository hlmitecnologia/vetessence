<?php

namespace App\Http\Controllers;

use App\Models\ControlledSubstance;
use App\Models\ControlledSubstanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlledSubstanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:estoque');
    }

    public function index(Request $request)
    {
        $query = ControlledSubstance::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('active_ingredient', 'like', "%{$request->search}%");
            });
        }

        if ($request->schedule) {
            $query->where('schedule', $request->schedule);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $substances = $query->orderBy('name')->get();

        return view('controlled-substances.index', compact('substances'));
    }

    public function create()
    {
        return redirect()->route('controlled-substances.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'schedule' => 'required|string|max:10',
            'anvisa_register' => 'nullable|string|max:50',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ControlledSubstance::create($validated);

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada cadastrada!');
    }

    public function show(ControlledSubstance $controlledSubstance)
    {
        $controlledSubstance->load('logs.user');
        return view('controlled-substances.show', compact('controlledSubstance'));
    }

    public function edit($controlledSubstance)
    {
        return redirect()->route('controlled-substances.index');
    }

    public function update(Request $request, ControlledSubstance $controlledSubstance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'schedule' => 'required|string|max:10',
            'anvisa_register' => 'nullable|string|max:50',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $controlledSubstance->update($validated);

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada atualizada!');
    }

    public function destroy(ControlledSubstance $controlledSubstance)
    {
        if ($controlledSubstance->logs()->count() > 0) {
            return back()->with('error', 'Não é possível excluir substância com movimentações registradas.');
        }

        $controlledSubstance->delete();

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada excluída!');
    }

    public function reportMonthly(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $logs = ControlledSubstanceLog::with('substance', 'user')
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->orderBy('created_at')
            ->get();

        $grouped = $logs->groupBy('substance.name');

        return view('controlled-substances.reports.monthly', compact('logs', 'grouped', 'month'));
    }

    public function reportAnnual(Request $request)
    {
        $year = $request->year ?? now()->year;
        $logs = ControlledSubstanceLog::with('substance')
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($l) => $l->created_at->format('m'));

        return view('controlled-substances.reports.annual', compact('logs', 'year'));
    }

    public function exportCsv(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $logs = ControlledSubstanceLog::with('substance', 'user')
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->orderBy('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=anvisa-report-{$month}.csv",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Data', 'Substância', 'Tipo', 'Quantidade', 'Usuário', 'Observações']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('d/m/Y H:i'),
                    $log->substance->name ?? 'N/A',
                    $log->type,
                    $log->quantity,
                    $log->user->name ?? 'N/A',
                    $log->observations ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
