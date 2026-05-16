<?php

namespace App\Http\Controllers;

use App\Models\CommunicationQueue;
use Illuminate\Http\Request;

class CommunicationQueueController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:communication.view')->only(['index', 'show']);
        $this->middleware('can:communication.create')->only(['create', 'store']);
        $this->middleware('can:communication.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CommunicationQueue::with(['tutor', 'pet', 'template']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        if ($request->channel) {
            $query->where('channel', $request->channel);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $queues = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('communication-queues.index', compact('queues'));
    }

    public function create()
    {
        return view('communication-queues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'pet_id' => 'nullable|exists:pets,id',
            'template_id' => 'nullable|exists:communication_templates,id',
            'channel' => 'required|string|max:50',
            'destination' => 'required|string|max:255',
            'message_content' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'sent_at' => 'nullable|date',
            'status' => 'required|string|max:50',
            'error_message' => 'nullable|string',
        ]);

        CommunicationQueue::create($validated);

        return redirect()->route('communication-queues.index')->with('success', 'Comunicação agendada com sucesso!');
    }

    public function show(CommunicationQueue $communicationQueue)
    {
        $communicationQueue->load(['tutor', 'pet', 'template']);
        return view('communication-queues.show', compact('communicationQueue'));
    }

    public function destroy(CommunicationQueue $communicationQueue)
    {
        $communicationQueue->delete();

        return redirect()->route('communication-queues.index')->with('success', 'Comunicação excluída!');
    }
}
