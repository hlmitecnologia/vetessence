<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:referral');
    }

    public function index(Request $request)
    {
        $query = Referral::with(['pet', 'referringVet', 'receivingVet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('referral_number', 'like', "%{$request->search}%");
        }

        $referrals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('referrals.index', compact('referrals'));
    }

    public function create()
    {
        $pets = Pet::with('tutors.user')->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('referrals.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'referring_vet_id' => 'required|exists:users,id',
            'referring_clinic' => 'nullable|string|max:255',
            'receiving_vet_id' => 'nullable|exists:users,id',
            'receiving_clinic' => 'nullable|string|max:255',
            'appointment_id' => 'nullable|exists:appointments,id',
            'reason' => 'required|string',
            'clinical_history' => 'nullable|string',
            'requested_procedures' => 'nullable|string',
            'attachments' => 'nullable|array',
            'status' => 'required|string|max:50',
            'response_notes' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);

        $validated['referral_number'] = Referral::generateNumber();

        Referral::create($validated);

        return redirect()->route('referrals.index')->with('success', 'Encaminhamento cadastrado com sucesso!');
    }

    public function show(Referral $referral)
    {
        $referral->load(['pet', 'referringVet', 'receivingVet', 'appointment']);
        return view('referrals.show', compact('referral'));
    }

    public function edit(Referral $referral)
    {
        $pets = Pet::with('tutors.user')->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('referrals.edit', compact('referral', 'pets', 'veterinarians'));
    }

    public function update(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'referring_vet_id' => 'required|exists:users,id',
            'referring_clinic' => 'nullable|string|max:255',
            'receiving_vet_id' => 'nullable|exists:users,id',
            'receiving_clinic' => 'nullable|string|max:255',
            'appointment_id' => 'nullable|exists:appointments,id',
            'reason' => 'required|string',
            'clinical_history' => 'nullable|string',
            'requested_procedures' => 'nullable|string',
            'attachments' => 'nullable|array',
            'status' => 'required|string|max:50',
            'response_notes' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);

        $referral->update($validated);

        return redirect()->route('referrals.index')->with('success', 'Encaminhamento atualizado com sucesso!');
    }

    public function destroy(Referral $referral)
    {
        $referral->delete();

        return redirect()->route('referrals.index')->with('success', 'Encaminhamento excluído!');
    }
}
