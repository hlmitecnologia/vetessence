<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZoonoticDisease;
use Illuminate\Http\Request;

class ZoonoticDiseaseController extends Controller
{
    public function index(Request $request)
    {
        $query = ZoonoticDisease::active();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('causative_agent', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function show($id)
    {
        $disease = ZoonoticDisease::active()->findOrFail($id);
        return response()->json($disease);
    }

    public function notifiable()
    {
        return response()->json(
            ZoonoticDisease::active()->notifiable()->orderBy('name')->get()
        );
    }

    public function categories()
    {
        return response()->json([
            'viral' => 'Viral',
            'bacterial' => 'Bacteriana',
            'parasitic' => 'Parasitária',
            'fungal' => 'Fúngica',
            'prion' => 'Prion',
        ]);
    }
}
