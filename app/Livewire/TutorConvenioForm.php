<?php

namespace App\Livewire;

use App\Models\Convenio;
use App\Models\ConvenioCoveredPet;
use App\Models\ConvenioSubscription;
use App\Models\Pet;
use App\Models\Tutor;
use Livewire\Component;

class TutorConvenioForm extends Component
{
    public ?Tutor $tutor = null;
    public ?ConvenioSubscription $subscription = null;
    public $convenio_id = '';
    public $policy_number = '';
    public $discount_percent = 0;
    public $start_date = '';
    public $end_date = '';
    public $is_active = true;
    public $selectedPets = [];

    protected $listeners = ['editSubscription' => 'load'];

    protected function rules()
    {
        return [
            'convenio_id' => 'required|exists:convenios,id',
            'policy_number' => 'nullable|string|max:100',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'selectedPets' => 'required|array|min:1',
            'selectedPets.*' => 'exists:pets,id',
        ];
    }

    public function mount(?Tutor $tutor = null)
    {
        $this->tutor = $tutor;
    }

    public function load($subscriptionId)
    {
        $this->subscription = ConvenioSubscription::with('convenio')->findOrFail($subscriptionId);
        $this->tutor = $this->subscription->tutor;
        $this->convenio_id = $this->subscription->convenio_id;
        $this->policy_number = $this->subscription->policy_number;
        $this->discount_percent = $this->subscription->discount_percent;
        $this->start_date = $this->subscription->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $this->subscription->end_date?->format('Y-m-d') ?? '';
        $this->is_active = $this->subscription->is_active;
        $this->selectedPets = $this->subscription->coveredPets->pluck('pet_id')->map(fn($id) => (string) $id)->toArray();
    }

    public function resetForm()
    {
        $this->subscription = null;
        $this->convenio_id = '';
        $this->policy_number = '';
        $this->discount_percent = 0;
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = true;
        $this->selectedPets = [];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tutor_id' => $this->tutor->id,
            'convenio_id' => $this->convenio_id,
            'policy_number' => $this->policy_number,
            'discount_percent' => $this->discount_percent,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->subscription) {
            $this->subscription->update($data);
            $this->subscription->coveredPets()->whereNotIn('pet_id', $this->selectedPets)->delete();
        } else {
            $this->subscription = ConvenioSubscription::create($data);
        }

        foreach ($this->selectedPets as $petId) {
            ConvenioCoveredPet::firstOrCreate([
                'subscription_id' => $this->subscription->id,
                'pet_id' => $petId,
            ]);
        }

        $this->dispatch('subscription-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $convenios = Convenio::where('is_active', true)->orderBy('name')->get();
        $pets = $this->tutor ? $this->tutor->pets()->orderBy('name')->get() : collect();

        return view('livewire.tutor-convenio-form', compact('convenios', 'pets'));
    }
}
