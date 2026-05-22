<div>
    <form wire:submit.prevent="save">
        <div class="form-group text-center">
            @if ($photo)
                <img src="{{ $photo->temporaryUrl() }}" class="rounded-circle mb-2" style="width: 96px; height: 96px; object-fit: cover;">
            @elseif ($petId && ($existing = \App\Models\Pet::find($petId)) && $existing->photo_url)
                <img src="{{ $existing->photo_url }}" class="rounded-circle mb-2" style="width: 96px; height: 96px; object-fit: cover;">
            @else
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 96px; height: 96px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white);">
                    <i class="fas fa-paw" style="font-size: 2rem; color: var(--brand-primary, #455e36);"></i>
                </div>
            @endif
            <div class="custom-file">
                <input type="file" wire:model="photo" class="custom-file-input" id="petPhoto" accept="image/png,image/jpeg,image/webp">
                <label class="custom-file-label" for="petPhoto">Foto do pet</label>
            </div>
            @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tutor Responsável *</label>
            <div class="input-group">
                <x-tom-select wire="tutor_id" :value="$tutor_id" required>
                    @foreach($tutors as $tutor)
                    <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                    @endforeach
                </x-tom-select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-primary" onclick="openNewTutorModal()" title="Novo Tutor">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            @error('tutor_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="modal fade" id="tutorModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Tutor</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @livewire('tutor-form', key('tutor-form-pet'))
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('close-modal', function() { $('#tutorModal').modal('hide'); });
            Livewire.on('tutor-saved', function() { location.reload(); });
        });
        function openNewTutorModal() {
            $('#tutorModal').modal('show');
        }
        </script>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Espécie *</label>
                    <select wire:model="species" class="form-control @error('species') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($speciesOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('species') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Gênero *</label>
                    <select wire:model="gender" class="form-control @error('gender') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="male">Macho</option>
                        <option value="female">Fêmea</option>
                    </select>
                    @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Raça</label>
                    <select wire:model="breed" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach($breeds as $breed)
                        <option value="{{ $breed }}">{{ $breed }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Porte</label>
                    <select wire:model="size" class="form-control">
                        <option value="small">Pequeno</option>
                        <option value="medium">Médio</option>
                        <option value="large">Grande</option>
                        <option value="giant">Gigante</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" wire:model="birth_date" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Peso (kg)</label>
                    <input type="number" wire:model="weight" step="0.01" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Cor/Pelagem</label>
                    <input type="text" wire:model="color" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Microchip</label>
                    <input type="text" wire:model="microchip" class="form-control" placeholder="Nº do microchip">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data do Microchip</label>
                    <input type="date" wire:model="microchip_date" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>RG Animal</label>
                    <input type="text" wire:model="rg_number" class="form-control" placeholder="Registro Geral do Animal">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Órgão Emissor RG</label>
                    <input type="text" wire:model="rg_issuer" class="form-control" placeholder="Ex: CFMV">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Observações</label>
                    <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
