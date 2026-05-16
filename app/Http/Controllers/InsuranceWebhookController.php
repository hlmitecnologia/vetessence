<?php

namespace App\Http\Controllers;

use App\Models\ConvenioClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InsuranceWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->validate([
            'external_id' => 'required|string',
            'status' => 'required|in:approved,rejected,pending',
            'amount_approved' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $claim = ConvenioClaim::where('external_id', $payload['external_id'])->first();

        if (!$claim) {
            Log::warning('Insurance webhook: claim not found', ['external_id' => $payload['external_id']]);
            return response()->json(['error' => 'Claim not found'], 404);
        }

        $claim->update([
            'status' => $payload['status'] === 'pending' ? 'filed' : $payload['status'],
            'amount_approved' => $payload['amount_approved'] ?? $claim->amount_approved,
            'response_at' => now(),
            'notes' => $payload['notes'] ?? $claim->notes,
        ]);

        Log::info('Insurance webhook processed', [
            'claim_id' => $claim->id,
            'external_id' => $payload['external_id'],
            'status' => $payload['status'],
        ]);

        return response()->json(['message' => 'Webhook processed']);
    }
}
