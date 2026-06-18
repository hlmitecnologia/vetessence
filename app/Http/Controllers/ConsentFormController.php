<?php

namespace App\Http\Controllers;

use App\Models\ConsentForm;
use App\Models\ConsentTemplate;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentFormController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsentForm::with(['pet', 'tutor', 'template', 'veterinarian', 'witness']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $consentForms = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('consent-forms.index', compact('consentForms'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $templates = ConsentTemplate::where('is_active', true)->get();
        $veterinarians = User::whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('consent-forms.create', compact('pets', 'templates', 'veterinarians', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'consent_template_id' => 'nullable|exists:consent_templates,id',
            'signed_content' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_document' => 'nullable|string|max:20',
            'veterinarian_id' => 'nullable|exists:users,id',
            'witness_id' => 'nullable|exists:users,id',
            'signed_at' => 'nullable|date',
            'signature_data' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = $request->boolean('mark_signed') ? 'signed' : 'draft';
        $validated['consent_number'] = ConsentForm::generateNumber();
        $validated['tutor_id'] = Pet::findOrFail($validated['pet_id'])->tutors()->first()?->id;

        ConsentForm::create($validated);

        return redirect()->route('consent-forms.index')->with('success', 'Termo de consentimento cadastrado!');
    }

    public function show(ConsentForm $consentForm)
    {
        $consentForm->load(['pet', 'tutor', 'appointment', 'template', 'veterinarian', 'witness']);

        return view('consent-forms.show', compact('consentForm'));
    }

    public function edit(ConsentForm $consentForm)
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $templates = ConsentTemplate::where('is_active', true)->get();
        $veterinarians = User::whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('consent-forms.edit', compact('consentForm', 'pets', 'templates', 'veterinarians', 'users'));
    }

    public function update(Request $request, ConsentForm $consentForm)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'consent_template_id' => 'nullable|exists:consent_templates,id',
            'signed_content' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_document' => 'nullable|string|max:20',
            'veterinarian_id' => 'nullable|exists:users,id',
            'witness_id' => 'nullable|exists:users,id',
            'signed_at' => 'nullable|date',
            'signature_data' => 'nullable|string',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['tutor_id'] = Pet::findOrFail($validated['pet_id'])->tutors()->first()?->id;

        $consentForm->update($validated);

        return redirect()->route('consent-forms.index')->with('success', 'Termo de consentimento atualizado!');
    }

    public function destroy(ConsentForm $consentForm)
    {
        $consentForm->delete();

        return redirect()->route('consent-forms.index')->with('success', 'Termo de consentimento excluído!');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:consent_templates,id',
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'nullable|exists:tutors,id',
        ]);

        $template = ConsentTemplate::findOrFail($request->template_id);
        $pet = Pet::with('tutors')->findOrFail($request->pet_id);
        $tutor = $request->tutor_id
            ? Tutor::findOrFail($request->tutor_id)
            : $pet->tutors()->first();

        $content = $template->content;
        $content = str_replace(
            ['{pet_name}', '{pet_species}', '{pet_breed}', '{tutor_name}', '{tutor_document}', '{date}'],
            [
                $pet->name,
                $pet->species,
                $pet->breed ?? 'não informada',
                $tutor->name,
                $tutor->document ?? 'não informado',
                now()->format('d/m/Y'),
            ],
            $content
        );

        return response()->json([
            'content' => $content,
            'template_name' => $template->name,
        ]);
    }
}
