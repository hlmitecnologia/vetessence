<?php

namespace App\Http\Controllers;

use App\Models\HealthCertificate;
use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HealthCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:certificado-sanitario');
    }
    public function index(Request $request)
    {
        $query = HealthCertificate::with(['pet', 'issuerVet']);

        if ($request->status) $query->where('status', $request->status);
        if ($request->type) $query->where('type', $request->type);
        if ($request->pet_id) $query->where('pet_id', $request->pet_id);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('certificate_number', 'like', "%{$request->search}%")
                  ->orWhereHas('pet', fn($p) => $p->where('name', 'like', "%{$request->search}%"));
            });
        }

        $certificates = $query->orderBy('issue_date', 'desc')->paginate(20);
        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('health-certificates.index', compact('certificates', 'pets'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        return view('health-certificates.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string|max:50',
            'destination' => 'nullable|string|max:255',
            'issuer_vet_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'clinical_notes' => 'nullable|string',
            'is_export' => 'boolean',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['certificate_number'] = HealthCertificate::generateNumber();

        HealthCertificate::create($validated);

        return redirect()->route('health-certificates.index')
            ->with('success', 'Certificado emitido com sucesso!');
    }

    public function show(HealthCertificate $healthCertificate)
    {
        $healthCertificate->load(['pet', 'issuerVet']);
        return view('health-certificates.show', compact('healthCertificate'));
    }

    public function edit(HealthCertificate $healthCertificate)
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        return view('health-certificates.edit', compact('healthCertificate', 'pets', 'veterinarians'));
    }

    public function update(Request $request, HealthCertificate $healthCertificate)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string|max:50',
            'destination' => 'nullable|string|max:255',
            'issuer_vet_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'clinical_notes' => 'nullable|string',
            'is_export' => 'boolean',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $healthCertificate->update($validated);

        return redirect()->route('health-certificates.index')
            ->with('success', 'Certificado atualizado!');
    }

    public function destroy(HealthCertificate $healthCertificate)
    {
        $healthCertificate->delete();
        return redirect()->route('health-certificates.index')
            ->with('success', 'Certificado excluído!');
    }

    public function pdf(HealthCertificate $healthCertificate)
    {
        $healthCertificate->load(['pet', 'issuerVet']);

        $healthCertificate->update([
            'pdf_generated_at' => now(),
            'status' => 'issued',
        ]);

        $view = $healthCertificate->is_cvi ? 'health-certificates.cvi-pdf' : 'health-certificates.pdf';
        $filename = $healthCertificate->is_cvi
            ? "cvi-" . str_replace('/', '-', $healthCertificate->cvi_number) . ".pdf"
            : "certificado-" . str_replace('/', '-', $healthCertificate->certificate_number) . ".pdf";

        $pdf = Pdf::loadView($view, compact('healthCertificate'));
        return $pdf->download($filename);
    }

    public function downloadCviPdf(HealthCertificate $healthCertificate)
    {
        $healthCertificate->load(['pet.tutors', 'issuerVet']);

        $healthCertificate->update([
            'pdf_generated_at' => now(),
            'status' => 'issued',
        ]);

        $filename = "cvi-" . str_replace('/', '-', $healthCertificate->cvi_number ?? $healthCertificate->certificate_number) . ".pdf";

        $pdf = Pdf::loadView('health-certificates.cvi-pdf', compact('healthCertificate'));
        return $pdf->download($filename);
    }

    protected function getVeterinarians()
    {
        $vetRole = Role::where('slug', 'veterinario')->first();
        if (!$vetRole) return collect();
        return User::where('role_id', $vetRole->id)->where('is_active', true)->orderBy('name')->get();
    }
}
