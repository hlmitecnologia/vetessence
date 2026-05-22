<?php

namespace App\Livewire;

use App\Models\Pet;
use App\Models\PetTutor;
use App\Models\Tutor;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PetForm extends Component
{
    use WithFileUploads;

    public $petId;
    public $name = '';
    public $tutor_id = '';
    public $species = '';
    public $breed = '';
    public $gender = '';
    public $birth_date = '';
    public $weight = '';
    public $color = '';
    public $microchip = '';
    public $microchip_date = '';
    public $rg_number = '';
    public $rg_issuer = '';
    public $size = 'medium';
    public $photo;
    public $notes = '';

    public $tutors = [];
    public $speciesOptions = [
        'canine' => 'Canino',
        'feline' => 'Felino',
        'avian' => 'Ave',
        'exotic' => 'Exótico',
        'reptile' => 'Réptil',
        'small_mammal' => 'Pequeno Mamífero'
    ];
    public $breeds = [];

    protected $rules = [
        'name' => 'required|string|max:100',
        'tutor_id' => 'required|exists:tutors,id',
        'species' => 'required|in:canine,feline,avian,exotic,reptile,small_mammal',
        'gender' => 'required|in:male,female',
    ];

    public function mount($id = null)
    {
        $this->tutors = Tutor::orderBy('name')->get();
        $this->updateBreeds();
        if ($id) $this->load($id);
    }

    #[On('editPet')]
    public function load($id)
    {
        $this->petId = $id;
        $pet = Pet::with('tutors')->findOrFail($id);
        $this->name = $pet->name;
        $this->tutor_id = (string) ($pet->tutors->first()->id ?? '');
        $this->species = $pet->species;
        $this->breed = $pet->breed ?? '';
        $this->gender = $pet->gender;
        $this->birth_date = $pet->birth_date ? $pet->birth_date->format('Y-m-d') : '';
        $this->weight = (string) ($pet->weight ?? '');
        $this->color = $pet->color ?? '';
        $this->microchip = $pet->microchip ?? '';
        $this->microchip_date = $pet->microchip_date ? $pet->microchip_date->format('Y-m-d') : '';
        $this->rg_number = $pet->rg_number ?? '';
        $this->rg_issuer = $pet->rg_issuer ?? '';
        $this->size = $pet->size ?? 'medium';
        $this->notes = $pet->notes ?? '';
        $this->tutors = Tutor::orderBy('name')->get();
        $this->updateBreeds();
    }

    #[On('createPetForTutor')]
    public function createForTutor($tutorId)
    {
        $this->resetForm();
        $this->tutor_id = (string) $tutorId;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->petId = null;
        $this->name = '';
        $this->tutor_id = '';
        $this->species = '';
        $this->breed = '';
        $this->gender = '';
        $this->birth_date = '';
        $this->weight = '';
        $this->color = '';
        $this->microchip = '';
        $this->microchip_date = '';
        $this->rg_number = '';
        $this->rg_issuer = '';
        $this->size = 'medium';
        $this->photo = null;
        $this->notes = '';
        $this->tutors = Tutor::orderBy('name')->get();
        $this->updateBreeds();
        $this->resetValidation();
    }

    public function updatedSpecies($value)
    {
        $this->updateBreeds();
    }

    public function updateBreeds()
    {
        $breedsBySpecies = [
            'canine' => ['SRD', 'Labrador', 'Golden Retriever', 'Poodle', 'Bulldog', 'Pastor Alemão', 'Rottweiler', 'Beagle', 'Vira-lata', 'Shih Tzu', 'Yorkshire', 'Dachshund', 'Boxer', 'Doberman', 'Pug', 'Husky', 'Border Collie', 'Outro'],
            'feline' => ['SRD', 'Persa', 'Siamês', 'Maine Coon', 'British Shorthair', 'Ragdoll', 'Bengal', 'Abissínio', 'Sphynx', 'Munchkin', 'Norueguês', 'Outro'],
            'avian' => ['Canário', 'Periquito', 'Calopsita', 'Papagaio', 'Arara', 'Cacatua', 'Curió', 'Bicudo', 'Outro'],
            'exotic' => ['Hamster', 'Porquinho da Índia', 'Coelho', 'Furão', 'Chinchila', 'Gerbil', 'Porco-espinho', 'Outro'],
            'reptile' => ['Tartaruga', 'Jabuti', 'Iguana', 'Gecko', 'Dragão barbudo', 'Serpente', 'Camaleão', 'Outro'],
            'small_mammal' => ['Hamster', 'Porquinho da Índia', 'Coelho anão', 'Furão', 'Chinchila', 'Gerbil', 'Rato', 'Camundongo', 'Outro'],
        ];
        $this->breeds = isset($breedsBySpecies[$this->species]) ? $breedsBySpecies[$this->species] : [];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed ?: null,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date ?: null,
            'weight' => $this->weight ?: null,
            'color' => $this->color ?: null,
            'microchip' => $this->microchip ?: null,
            'microchip_date' => $this->microchip_date ?: null,
            'rg_number' => $this->rg_number ?: null,
            'rg_issuer' => $this->rg_issuer ?: null,
            'size' => $this->size,
            'notes' => $this->notes ?: null,
            'is_active' => true,
        ];

        if ($this->petId) {
            Pet::findOrFail($this->petId)->update($data);
            PetTutor::where('pet_id', $this->petId)->where('is_primary', true)->update(['tutor_id' => $this->tutor_id]);
        } else {
            $pet = Pet::create($data);
            PetTutor::create([
                'pet_id' => $pet->id,
                'tutor_id' => $this->tutor_id,
                'is_primary' => true,
                'relationship' => 'proprietário',
            ]);
        }

        $this->dispatch('pet-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.pet-form');
    }
}
