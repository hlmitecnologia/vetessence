<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function handle(Request $request, PaymentGateway $gateway)
    {
        $payload = $request->all();

        $result = $this->paymentService->processWebhook($gateway, $payload);

        if (!$result['success']) {
            logger()->warning('Payment webhook failed', [
                'gateway_id' => $gateway->id,
                'provider' => $gateway->provider,
                'result' => $result,
            ]);
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
