<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use App\Models\Exam;
use App\Models\Surgery;
use App\Models\Hospitalization;
use App\Models\Invoice;

class PatientTimelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pets.view');
    }

    public function index(Pet $pet)
    {
        $pet->load('tutors');

        $events = collect();

        foreach ($pet->medicalRecords as $r) {
            $events->push([
                'date' => $r->created_at,
                'type' => 'Prontuário',
                'icon' => 'fa-notes-medical',
                'color' => 'success',
                'summary' => $r->diagnosis ?? $r->chief_complaint ?? 'Atendimento',
                'url' => route('medical-records.show', $r),
            ]);
        }

        foreach ($pet->vaccinations as $v) {
            $events->push([
                'date' => $v->date,
                'type' => 'Vacina',
                'icon' => 'fa-syringe',
                'color' => 'info',
                'summary' => $v->vaccine . ($v->batch ? " (lote {$v->batch})" : ''),
                'url' => route('vaccinations.show', $v),
            ]);
        }

        foreach ($pet->appointments as $a) {
            $events->push([
                'date' => $a->start_time,
                'type' => 'Consulta',
                'icon' => 'fa-calendar-check',
                'color' => 'primary',
                'summary' => 'Status: ' . ($a->status ?? 'agendado'),
                'url' => route('appointments.show', $a),
            ]);
        }

        foreach ($pet->exams as $e) {
            $events->push([
                'date' => $e->date ?? $e->created_at,
                'type' => 'Exame',
                'icon' => 'fa-flask',
                'color' => 'warning',
                'summary' => $e->type ?? $e->name ?? 'Exame',
                'url' => route('exams.show', $e),
            ]);
        }

        foreach ($pet->surgeries as $s) {
            $events->push([
                'date' => $s->date ?? $s->created_at,
                'type' => 'Cirurgia',
                'icon' => 'fa-user-md',
                'color' => 'danger',
                'summary' => $s->procedure ?? $s->name ?? 'Procedimento cirúrgico',
                'url' => route('surgeries.show', $s),
            ]);
        }

        $hospitalizations = Hospitalization::where('pet_id', $pet->id)->get();
        foreach ($hospitalizations as $h) {
            $events->push([
                'date' => $h->admission_date,
                'type' => 'Internação',
                'icon' => 'fa-procedures',
                'color' => 'secondary',
                'summary' => 'Admitido: ' . ($h->reason ?? '') . ($h->discharge_date ? ' | Alta: ' . $h->discharge_date->format('d/m/Y') : ''),
                'url' => route('hospitalizations.show', $h),
            ]);
        }

        foreach ($pet->invoices as $i) {
            $events->push([
                'date' => $i->created_at,
                'type' => 'Fatura',
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'dark',
                'summary' => 'R$ ' . number_format($i->total, 2, ',', '.') . ' — ' . ($i->status ?? ''),
                'url' => route('invoices.show', $i),
            ]);
        }

        $events = $events->sortByDesc('date')->values();

        return view('pets.timeline', compact('pet', 'events'));
    }
}
