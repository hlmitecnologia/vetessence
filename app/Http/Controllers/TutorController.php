<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Tutor::with('user');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('cpf', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
            $query->orWhereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $tutors = $query->join('users', 'tutors.user_id', '=', 'users.id')
            ->select('tutors.*')
            ->orderBy('users.name')
            ->paginate(15)
            ->withQueryString();

        return view('tutors.index', compact('tutors'));
    }

    public function create()
    {
        return view('tutors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|cpf|unique:tutors',
            'email' => 'required|email|unique:tutors',
            'phone' => 'required',
            'address' => 'nullable|string',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $validated['notify_sms'] = $request->boolean('notify_sms');
        $validated['notify_whatsapp'] = $request->boolean('notify_whatsapp');
        $validated['notify_email'] = $request->boolean('notify_email');

        DB::beginTransaction();
        try {
            $tutor = Tutor::create($validated);
            DB::commit();
            return redirect()->route('tutors.index')->with('success', 'Tutor cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar tutor.')->withInput();
        }
    }

    public function show(Tutor $tutor)
    {
        $tutor->load(['pets', 'invoices']);
        return view('tutors.show', compact('tutor'));
    }

    public function edit(Tutor $tutor)
    {
        return view('tutors.edit', compact('tutor'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|cpf|unique:tutors,cpf,' . $tutor->id,
            'email' => 'required|email|unique:tutors,email,' . $tutor->id,
            'phone' => 'required',
            'address' => 'nullable|string',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $validated['notify_sms'] = $request->boolean('notify_sms');
        $validated['notify_whatsapp'] = $request->boolean('notify_whatsapp');
        $validated['notify_email'] = $request->boolean('notify_email');
        $tutor->update($validated);

        return redirect()->route('tutors.index')->with('success', 'Tutor atualizado com sucesso!');
    }

    public function communication(Tutor $tutor)
    {
        $logs = NotificationLog::where('tutor_id', $tutor->id)
            ->latest()
            ->paginate(20);

        return view('tutors.communication', compact('tutor', 'logs'));
    }

    public function destroy(Tutor $tutor)
    {
        if ($tutor->pets()->count() > 0) {
            return back()->with('error', 'Não é possível excluir tutor com pets vinculados.');
        }

        $tutor->delete();

        return redirect()->route('tutors.index')->with('success', 'Tutor excluído com sucesso!');
    }
}
