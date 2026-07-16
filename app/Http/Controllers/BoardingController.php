<?php

namespace App\Http\Controllers;

use App\Models\Boarding;
use App\Models\BoardingDailyTask;
use App\Models\BoardingKennel;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Pet;
use Illuminate\Http\Request;
use App\Services\ConvenioService;

class BoardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:hospedagem');
    }
    public function index(Request $request)
    {
        $query = Boarding::with(['pet', 'createdBy']);

        if ($request->search) {
            $query->whereHas('pet', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $boardings = $query->orderBy('check_in_at', 'desc')->get();

        return view('boardings.index', compact('boardings'));
    }

    public function create()
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        return view('boardings.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|in:boarding,grooming,both',
            'check_in_at' => 'required|date',
            'expected_check_out' => 'nullable|date|after_or_equal:check_in_at',
            'daily_rate' => 'required|numeric|min:0',
            'grooming_fee' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'feeding_instructions' => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'pickup_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['grooming_fee'] = $request->grooming_fee ?? 0;
        $validated['total_amount'] = $validated['daily_rate'];
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'checked_in';

        Boarding::create($validated);

        return redirect()->route('boardings.index')
            ->with('success', 'Hospedagem registrada com sucesso!');
    }

    public function show(Boarding $boarding)
    {
        $boarding->load(['pet', 'createdBy', 'checkedOutBy', 'dailyTasks.completedBy']);
        return view('boardings.show', compact('boarding'));
    }

    public function edit(Boarding $boarding)
    {
        if ($boarding->status === 'checked_out') {
            return back()->with('error', 'Não é possível editar uma hospedagem finalizada.');
        }
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        return view('boardings.edit', compact('boarding', 'pets'));
    }

    public function update(Request $request, Boarding $boarding)
    {
        if ($boarding->status === 'checked_out') {
            return back()->with('error', 'Não é possível editar uma hospedagem finalizada.');
        }

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|in:boarding,grooming,both',
            'check_in_at' => 'required|date',
            'expected_check_out' => 'nullable|date|after_or_equal:check_in_at',
            'daily_rate' => 'required|numeric|min:0',
            'grooming_fee' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'feeding_instructions' => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'pickup_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['grooming_fee'] = $request->grooming_fee ?? 0;
        $boarding->update($validated);

        return redirect()->route('boardings.show', $boarding)
            ->with('success', 'Hospedagem atualizada com sucesso!');
    }

    public function destroy(Boarding $boarding)
    {
        if ($boarding->status === 'checked_in') {
            return back()->with('error', 'Finalize a hospedagem antes de excluir.');
        }
        $boarding->delete();

        return redirect()->route('boardings.index')
            ->with('success', 'Hospedagem excluída com sucesso!');
    }

    public function checkout(Request $request, Boarding $boarding)
    {
        if ($boarding->status !== 'checked_in') {
            return back()->with('error', 'Hospedagem já finalizada ou cancelada.');
        }

        $validated = $request->validate([
            'check_out_at' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $boarding->update([
            'check_out_at' => $validated['check_out_at'],
            'total_amount' => $validated['total_amount'],
            'status' => 'checked_out',
            'checked_out_by' => auth()->id(),
            'notes' => $validated['notes'] ?? $boarding->notes,
        ]);

        $boarding->load('pet.tutors');

        $tutor = $boarding->pet->tutors()
            ->wherePivot('is_primary', true)
            ->first() ?? $boarding->pet->tutors()->first();

        if ($tutor && $validated['total_amount'] > 0) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'pet_id' => $boarding->pet_id,
                'tutor_id' => $tutor->id,
                'user_id' => auth()->id(),
                'branch_id' => $boarding->branch_id,
                'boarding_id' => $boarding->id,
                'status' => 'pending',
                'total' => $validated['total_amount'],
                'subtotal' => $validated['total_amount'],
                'due_date' => now(),
            ]);

            $days = $boarding->daysBoarded();
            $desc = $boarding->type === 'grooming' ? 'Banho/Tosa' : 'Hospedagem';

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "{$desc} — {$days} diária(s)",
                'quantity' => $days,
                'unit_price' => $boarding->daily_rate,
                'total' => $boarding->daily_rate * $days,
                'item_type' => 'service',
            ]);

            if (($boarding->grooming_fee ?? 0) > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Taxa de Banho/Tosa',
                    'quantity' => 1,
                    'unit_price' => $boarding->grooming_fee,
                    'total' => $boarding->grooming_fee,
                    'item_type' => 'service',
                ]);
            }

            // Aplicar desconto de convênio
            $convenioService = app(ConvenioService::class);
            $subscription = $convenioService->findActiveSubscription($tutor, $boarding->pet);
            if ($subscription) {
                $invoice->refresh();
                $convenioService->applyDiscount($invoice, $subscription);
            }

            $invoice->refresh();
            $boarding->update(['total_amount' => $invoice->total]);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Check-out realizado! Fatura gerada: ' . $invoice->invoice_number);
        }

        return redirect()->route('boardings.show', $boarding)
            ->with('success', 'Check-out realizado com sucesso!');
    }

    public function cancel(Boarding $boarding)
    {
        if ($boarding->status !== 'checked_in') {
            return back()->with('error', 'Hospedagem já finalizada ou cancelada.');
        }

        $boarding->update(['status' => 'cancelled']);

        return redirect()->route('boardings.show', $boarding)
            ->with('success', 'Hospedagem cancelada.');
    }

    public function completeTask(Request $request, Boarding $boarding, BoardingDailyTask $task)
    {
        if ($boarding->id !== $task->boarding_id) {
            abort(404);
        }

        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => auth()->id(),
            'observations' => $request->observations,
        ]);

        return back()->with('success', 'Tarefa concluída!');
    }

    public function storeTask(Request $request, Boarding $boarding)
    {
        $validated = $request->validate([
            'task_date' => 'required|date',
            'task_name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $boarding->dailyTasks()->create($validated + ['is_completed' => false]);

        return back()->with('success', 'Tarefa adicionada!');
    }

    public function destroyTask(Boarding $boarding, BoardingDailyTask $task)
    {
        if ($boarding->id !== $task->boarding_id) abort(404);
        $task->delete();

        return back()->with('success', 'Tarefa excluída.');
    }

    public function active()
    {
        $boardings = Boarding::with(['pet', 'createdBy'])
            ->active()
            ->orderBy('check_in_at')
->get();

        return view('boardings.index', compact('boardings'));
    }

    public function kennelMap()
    {
        $kennels = BoardingKennel::with('activeBoardings.pet')->where('is_active', true)->get();
        $activeBoardings = Boarding::active()->with('pet', 'kennel')->get();
        return view('boardings.kennel-map', compact('kennels', 'activeBoardings'));
    }

    public function assignKennel(Request $request, Boarding $boarding)
    {
        $data = $request->validate([
            'kennel_id' => 'required|exists:boarding_kennels,id',
        ]);

        $boarding->update(['kennel_id' => $data['kennel_id']]);

        return redirect()->back()->with('success', 'Canil atribuído com sucesso!');
    }
}
