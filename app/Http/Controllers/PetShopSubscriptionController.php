<?php

namespace App\Http\Controllers;

use App\Models\PetShopSubscription;
use App\Models\PetShopPackage;
use App\Models\PetShopConsumption;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetShopSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pet-shop-subscriptions.view')->only(['index', 'show']);
        $this->middleware('can:pet-shop-subscriptions.create')->only(['store']);
        $this->middleware('can:pet-shop-subscriptions.edit')->only(['cancel']);
    }

    public function index()
    {
        $subscriptions = PetShopSubscription::with(['pet', 'package', 'branch'])
            ->orderBy('created_at', 'desc')->get();
        return view('pet-shop-subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $packages = PetShopPackage::active()->orderBy('name')->get();
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('pet-shop-subscriptions.create', compact('packages', 'pets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'package_id' => 'required|exists:pet_shop_packages,id',
            'branch_id' => 'required|exists:branches,id',
            'start_date' => 'required|date',
            'auto_renew' => 'boolean',
        ]);

        $package = PetShopPackage::findOrFail($data['package_id']);

        $data['remaining_uses'] = $package->max_uses;
        $data['total_uses'] = $package->max_uses;
        $data['total_savings'] = 0;
        $data['status'] = 'active';
        $data['auto_renew'] = $request->boolean('auto_renew', false);

        if ($package->validity_days) {
            $data['end_date'] = \Carbon\Carbon::parse($data['start_date'])
                ->addDays($package->validity_days);
        }

        PetShopSubscription::create($data);

        return redirect()->route('pet-shop-subscriptions.index')
            ->with('success', 'Assinatura criada com sucesso.');
    }

    public function show(PetShopSubscription $petShopSubscription)
    {
        $petShopSubscription->load(['pet', 'package', 'branch', 'consumptions.service', 'consumptions.user']);
        return view('pet-shop-subscriptions.show', compact('petShopSubscription'));
    }

    public function cancel(PetShopSubscription $petShopSubscription)
    {
        $petShopSubscription->update(['status' => 'cancelled']);

        return redirect()->route('pet-shop-subscriptions.index')
            ->with('success', 'Assinatura cancelada.');
    }
}
