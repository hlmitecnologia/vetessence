<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:auditoria');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $model = $request->input('model_type', $request->input('model'));
        if ($model) {
            $modelType = str_starts_with($model, 'App\\Models\\')
                ? $model
                : 'App\\Models\\' . $model;
            $query->where('model_type', $modelType);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->get();

        return view('audit-logs.index', compact('logs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }
}
