<?php

namespace App\Livewire;

use App\Models\Tutor;
use Livewire\Component;
use Livewire\WithFileUploads;

class PetForm extends Component
{
    use WithFileUploads;

    public $name = '';
    public $tutor_id = '';
    public $species = '';
    public $breed = '';
    public $gender = '';
    public $birth_date = '';
    public $weight = '';
    public $color = '';
    public $microchip = '';
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

    public function mount($tutorId = null)
    {
        $this->tutors = Tutor::orderBy('name')->get();
        if ($tutorId) {
            $this->tutor_id = $tutorId;
        }
        $this->updateBreeds();
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

        $pet = \App\Models\Pet::create([
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date ?: null,
            'weight' => $this->weight ?: null,
            'color' => $this->color ?: null,
            'microchip' => $this->microchip ?: null,
            'size' => $this->size,
            'notes' => $this->notes ?: null,
            'is_active' => true,
        ]);

        \App\Models\PetTutor::create([
            'pet_id' => $pet->id,
            'tutor_id' => $this->tutor_id,
            'is_primary' => true,
            'relationship' => 'proprietário',
        ]);

        session()->flash('success', 'Pet cadastrado com sucesso!');
        return redirect()->route('pets.index');
    }

    public function render()
    {
        return view('livewire.pet-form');
    }
}
