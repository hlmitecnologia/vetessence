<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use Illuminate\Http\Request;

class ExecutionMapController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:execution-maps');
    }

    public function index()
    {
        return view('execution-maps.index');
    }

    public function show(Hospitalization $hospitalization, $date = null)
    {
        $fragment = 'execucao';
        return redirect()->route('hospitalizations.show', $hospitalization->id)
            ->with('activeTab', $fragment);
    }
}
