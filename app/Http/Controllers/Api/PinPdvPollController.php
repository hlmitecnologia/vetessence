<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\Payment\PaymentGatewayProviderFactory;

class PinPdvPollController extends Controller
{
    public function query(string $identifier, PaymentGatewayProviderFactory $factory)
    {
        $gateway = PaymentGateway::withoutBranch()->active()
            ->where('provider', 'multicard')
            ->whereIn('channel', ['pdv', 'both'])
            ->first();

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway MultiplusCard ativo encontrado.',
            ], 404);
        }

        $provider = $factory->make($gateway);
        $result = $provider->queryPreVenda($identifier);

        return response()->json($result);
    }
}
