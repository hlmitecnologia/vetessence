<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $pets = auth()->user()->pets()->with('exams')->get();
        $exams = $pets->flatMap->exams->sortByDesc('date');
        return view('portal.exams.index', compact('exams'));
    }
}
