<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabEquipmentIntegration;
use App\Models\LabEquipmentResult;
use Illuminate\Http\Request;

class LabEquipmentController extends Controller
{
    public function receive(Request $request, $integrationId)
    {
        $integration = LabEquipmentIntegration::findOrFail($integrationId);

        if (!$integration->is_active) {
            return response()->json(['error' => 'Integration inactive'], 403);
        }

        $validated = $request->validate([
            'result_identifier' => 'nullable|string|max:255',
            'pet_id' => 'nullable|exists:pets,id',
            'laboratory_order_id' => 'nullable|exists:laboratory_orders,id',
            'test_type' => 'required|string|max:100',
            'raw_data' => 'required|array',
        ]);

        $result = LabEquipmentResult::create([
            'integration_id' => $integration->id,
            'result_identifier' => $validated['result_identifier'],
            'pet_id' => $validated['pet_id'] ?? null,
            'laboratory_order_id' => $validated['laboratory_order_id'] ?? null,
            'test_type' => $validated['test_type'],
            'raw_data' => $validated['raw_data'],
            'status' => 'received',
            'received_at' => now(),
        ]);

        $integration->update(['last_contact_at' => now()]);

        return response()->json([
            'success' => true,
            'result_id' => $result->id,
            'message' => 'Result received successfully',
        ], 201);
    }

    public function status($integrationId)
    {
        $integration = LabEquipmentIntegration::findOrFail($integrationId);
        return response()->json([
            'id' => $integration->id,
            'name' => $integration->name,
            'is_active' => $integration->is_active,
            'last_contact_at' => $integration->last_contact_at,
        ]);
    }
}
