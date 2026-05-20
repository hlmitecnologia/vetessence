<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\NfseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NfseWebhookController extends Controller
{
    public function __invoke(Request $request, Branch $branch)
    {
        Log::info('NFSe webhook received', [
            'branch_id' => $branch->id,
            'payload' => $request->all(),
        ]);

        $nfseNumber = $request->input('nfse_number');
        $status = $request->input('status');

        if ($nfseNumber && $status) {
            NfseInvoice::where('branch_id', $branch->id)
                ->where('nfse_number', $nfseNumber)
                ->update(['status' => $status]);
        }

        return response()->json(['message' => 'ok']);
    }
}
