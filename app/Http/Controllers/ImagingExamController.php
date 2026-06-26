<?php

namespace App\Http\Controllers;

use App\Models\ImagingExam;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class ImagingExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:imagem');
    }

    public function index(Request $request)
    {
        $query = ImagingExam::with(['pet', 'vet', 'radiologist']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->exam_type) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->search) {
            $query->where('exam_number', 'like', "%{$request->search}%");
        }

        $exams = $query->orderBy('exam_date', 'desc')->get();

        return view('imaging-exams.index', compact('exams'));
    }

    public function create()
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('imaging-exams.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'exam_type' => 'required|string|max:100',
            'region' => 'nullable|string|max:255',
            'findings' => 'nullable|string',
            'impression' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'images' => 'nullable|array',
            'status' => 'required|string|max:50',
            'radiologist_id' => 'nullable|exists:users,id',
            'exam_date' => 'required|date',
        ]);

        $validated['exam_number'] = ImagingExam::generateNumber();

        ImagingExam::create($validated);

        return redirect()->route('imaging-exams.index')->with('success', 'Exame de imagem cadastrado!');
    }

    public function show(ImagingExam $imagingExam)
    {
        $imagingExam->load(['pet', 'vet', 'appointment', 'radiologist']);
        $exam = $imagingExam;
        return view('imaging-exams.show', compact('exam'));
    }

    public function edit(ImagingExam $imagingExam)
    {
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('imaging-exams.edit', compact('imagingExam', 'veterinarians'));
    }

    public function update(Request $request, ImagingExam $imagingExam)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'exam_type' => 'required|string|max:100',
            'region' => 'nullable|string|max:255',
            'findings' => 'nullable|string',
            'impression' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'images' => 'nullable|array',
            'status' => 'required|string|max:50',
            'radiologist_id' => 'nullable|exists:users,id',
            'exam_date' => 'required|date',
        ]);

        $imagingExam->update($validated);

        return redirect()->route('imaging-exams.index')->with('success', 'Exame de imagem atualizado!');
    }

    public function destroy(ImagingExam $imagingExam)
    {
        $imagingExam->delete();

        return redirect()->route('imaging-exams.index')->with('success', 'Exame de imagem excluído!');
    }
}
