<?php

namespace App\Http\Controllers;

use App\Models\PetShopConsumption;
use App\Models\PetShopSubscription;
use App\Models\Service;
use Illuminate\Http\Request;

class PetShopConsumptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pet-shop-subscriptions.view');
    }

    public function index()
    {
        $consumptions = PetShopConsumption::with(['subscription.pet', 'subscription.package', 'service', 'user'])
            ->orderBy('service_date', 'desc')->get();
        return view('pet-shop-consumptions.index', compact('consumptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subscription_id' => 'required|exists:pet_shop_subscriptions,id',
            'service_id' => 'required|exists:services,id',
            'service_date' => 'required|date',
            'boarding_id' => 'nullable|exists:boardings,id',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        $subscription = PetShopSubscription::findOrFail($data['subscription_id']);

        if ($subscription->status !== 'active') {
            return back()->with('error', 'Assinatura não está ativa.');
        }

        if ($subscription->remaining_uses <= 0) {
            return back()->with('error', 'Assinatura sem usos restantes.');
        }

        $servicePrice = Service::findOrFail($data['service_id'])->price ?? 0;
        $packagePrice = count($subscription->package->services) > 0
            ? $subscription->package->total_price / $subscription->package->max_uses
            : 0;
        $savings = max(0, $servicePrice - $packagePrice);

        $data['used_by'] = auth()->id();
        $data['savings_amount'] = $savings;

        PetShopConsumption::create($data);

        $subscription->decrement('remaining_uses');
        $subscription->increment('total_savings', $savings);

        if ($subscription->remaining_uses <= 0) {
            $subscription->update(['status' => 'completed']);
        }

        return redirect()->route('pet-shop-subscriptions.show', $subscription)
            ->with('success', 'Consumo registrado. Economia de R$ ' . number_format($savings, 2, ',', '.'));
    }
}
