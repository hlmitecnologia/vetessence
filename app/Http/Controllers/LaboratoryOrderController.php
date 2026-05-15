<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryOrder;
use App\Models\LaboratoryTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaboratoryOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = LaboratoryOrder::with(['pet', 'vet', 'tests']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);

        return view('laboratory-orders.index', compact('orders'));
    }

    public function create()
    {
        return view('laboratory-orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'lab_name' => 'nullable|string|max:255',
            'order_date' => 'required|date',
            'result_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'tests' => 'nullable|array',
            'tests.*.test_name' => 'required_with:tests|string|max:255',
            'tests.*.test_code' => 'nullable|string|max:50',
            'tests.*.reference_range' => 'nullable|string',
            'tests.*.unit' => 'nullable|string|max:50',
            'tests.*.observations' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['order_number'] = LaboratoryOrder::generateNumber();
            $tests = $validated['tests'] ?? [];
            unset($validated['tests']);

            $order = LaboratoryOrder::create($validated);

            foreach ($tests as $testData) {
                $testData['laboratory_order_id'] = $order->id;
                LaboratoryTest::create($testData);
            }

            DB::commit();
            return redirect()->route('laboratory-orders.index')->with('success', 'Pedido laboratorial cadastrado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar pedido laboratorial.')->withInput();
        }
    }

    public function show(LaboratoryOrder $laboratoryOrder)
    {
        $laboratoryOrder->load(['pet', 'vet', 'appointment', 'tests']);
        $order = $laboratoryOrder;
        return view('laboratory-orders.show', compact('order'));
    }

    public function edit(LaboratoryOrder $laboratoryOrder)
    {
        $laboratoryOrder->load('tests');
        return view('laboratory-orders.edit', compact('laboratoryOrder'));
    }

    public function update(Request $request, LaboratoryOrder $laboratoryOrder)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'lab_name' => 'nullable|string|max:255',
            'order_date' => 'required|date',
            'result_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'tests' => 'nullable|array',
            'tests.*.test_name' => 'required_with:tests|string|max:255',
            'tests.*.test_code' => 'nullable|string|max:50',
            'tests.*.result' => 'nullable|string',
            'tests.*.reference_range' => 'nullable|string',
            'tests.*.unit' => 'nullable|string|max:50',
            'tests.*.is_abnormal' => 'boolean',
            'tests.*.observations' => 'nullable|string',
            'tests.*.id' => 'nullable|exists:laboratory_tests,id',
        ]);

        DB::beginTransaction();
        try {
            $tests = $validated['tests'] ?? [];
            unset($validated['tests']);

            $laboratoryOrder->update($validated);

            $existingIds = [];
            foreach ($tests as $testData) {
                $testData['is_abnormal'] = $testData['is_abnormal'] ?? false;

                if (isset($testData['id'])) {
                    LaboratoryTest::where('id', $testData['id'])
                        ->where('laboratory_order_id', $laboratoryOrder->id)
                        ->update($testData);
                    $existingIds[] = $testData['id'];
                } else {
                    $testData['laboratory_order_id'] = $laboratoryOrder->id;
                    $test = LaboratoryTest::create($testData);
                    $existingIds[] = $test->id;
                }
            }

            LaboratoryTest::where('laboratory_order_id', $laboratoryOrder->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();
            return redirect()->route('laboratory-orders.index')->with('success', 'Pedido laboratorial atualizado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar pedido laboratorial.')->withInput();
        }
    }

    public function destroy(LaboratoryOrder $laboratoryOrder)
    {
        $laboratoryOrder->tests()->delete();
        $laboratoryOrder->delete();

        return redirect()->route('laboratory-orders.index')->with('success', 'Pedido laboratorial excluído!');
    }
}
