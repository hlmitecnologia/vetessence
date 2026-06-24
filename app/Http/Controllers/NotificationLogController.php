<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:notification-logs.view')->only(['index', 'show']);
        $this->middleware('can:notification-logs.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = NotificationLog::with(['pet', 'tutor']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->channel) {
            $query->where('channel', $request->channel);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('sent_at', 'desc')->get();

        return view('notification-logs.index', compact('logs'));
    }

    public function show(NotificationLog $notificationLog)
    {
        $notificationLog->load(['pet', 'tutor']);
        return view('notification-logs.show', compact('notificationLog'));
    }

    public function destroy(NotificationLog $notificationLog)
    {
        $notificationLog->delete();

        return redirect()->route('notification-logs.index')
            ->with('success', 'Log de notificação excluído!');
    }
}
