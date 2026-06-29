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

        if ($result['success']) {
            logger()->info('Payment webhook processed', [
                'gateway_id' => $gateway->id,
                'invoice_id' => $result['invoice_id'] ?? null,
                'new_status' => $result['new_status'] ?? null,
            ]);
        } else {
            logger()->warning('Payment webhook not processed', [
                'gateway_id' => $gateway->id,
                'provider' => $gateway->provider,
                'reason' => $result['message'],
                'sandbox' => $gateway->is_sandbox,
            ]);
        }

        // Mercado Pago / PagSeguro / Stone esperam 200 mesmo para notificações
        // que não puderam ser processadas (testes, notificações duplicadas, etc.)
        return response()->json($result);
    }
}
