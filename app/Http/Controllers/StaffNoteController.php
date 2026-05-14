<?php

namespace App\Http\Controllers;

use App\Models\StaffNote;
use App\Models\User;
use Illuminate\Http\Request;

class StaffNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:nota-interna');
    }
    public function index(Request $request)
    {
        $query = StaffNote::with(['creator', 'assignedTo']);

        if ($request->tab === 'inbox' || !$request->tab) {
            $query->where('assigned_to', auth()->id())->orWhere(function ($q) {
                $q->whereNull('assigned_to')->where('created_by', '!=', auth()->id());
            });
        } elseif ($request->tab === 'sent') {
            $query->where('created_by', auth()->id());
        } elseif ($request->tab === 'unread') {
            $query->where(function ($q) {
                $q->where('assigned_to', auth()->id())->orWhereNull('assigned_to');
            })->where('is_read', false)->where('created_by', '!=', auth()->id());
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = StaffNote::where(function ($q) {
            $q->where('assigned_to', auth()->id())->orWhereNull('assigned_to');
        })->where('is_read', false)->where('created_by', '!=', auth()->id())->count();

        return view('staff-notes.index', compact('notes', 'unreadCount'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('staff-notes.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = auth()->id();

        StaffNote::create($validated);

        return redirect()->route('staff-notes.index')
            ->with('success', 'Nota interna cadastrada com sucesso!');
    }

    public function show(StaffNote $staffNote)
    {
        if ($staffNote->assigned_to === auth()->id() && !$staffNote->is_read) {
            $staffNote->markAsRead();
        }

        $staffNote->load(['creator', 'assignedTo']);
        return view('staff-notes.show', compact('staffNote'));
    }

    public function edit(StaffNote $staffNote)
    {
        if ($staffNote->created_by !== auth()->id()) {
            return back()->with('error', 'Você só pode editar suas próprias notas.');
        }
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('staff-notes.edit', compact('staffNote', 'users'));
    }

    public function update(Request $request, StaffNote $staffNote)
    {
        if ($staffNote->created_by !== auth()->id()) {
            return back()->with('error', 'Você só pode editar suas próprias notas.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:100',
        ]);

        $staffNote->update($validated);

        return redirect()->route('staff-notes.show', $staffNote)
            ->with('success', 'Nota atualizada com sucesso!');
    }

    public function destroy(StaffNote $staffNote)
    {
        if ($staffNote->created_by !== auth()->id()) {
            return back()->with('error', 'Você só pode excluir suas próprias notas.');
        }

        $staffNote->delete();

        return redirect()->route('staff-notes.index')
            ->with('success', 'Nota excluída com sucesso!');
    }

    public function markRead(StaffNote $staffNote)
    {
        $staffNote->markAsRead();
        return back()->with('success', 'Nota marcada como lida.');
    }

    public function inbox()
    {
        $notes = StaffNote::with(['creator', 'assignedTo'])
            ->where(function ($q) {
                $q->where('assigned_to', auth()->id())->orWhereNull('assigned_to');
            })
            ->where('created_by', '!=', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff-notes.index', compact('notes'));
    }
}
