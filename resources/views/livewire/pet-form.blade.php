<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tutor Responsável *</label>
            <select wire:model="tutor_id" class="form-control @error('tutor_id') is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach($tutors as $tutor)
                <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                @endforeach
            </select>
            @error('tutor_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

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
                    <input type="text" wire:model="microchip" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="form-control" rows="2"></textarea>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
