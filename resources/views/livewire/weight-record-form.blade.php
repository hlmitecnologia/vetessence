<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Pet *</label>
            <x-tom-select wire="pet_id" :value="$pet_id" required>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                @endforeach
            </x-tom-select>
            @error('pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Peso (kg) *</label>
                    <input type="number" wire:model="weight" class="form-control @error('weight') is-invalid @enderror" step="0.01" min="0" required>
                    @error('weight') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>ECC (1-9)</label>
                    <input type="number" wire:model="bcs" class="form-control @error('bcs') is-invalid @enderror" min="1" max="9" step="0.5">
                    @error('bcs') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Data da Medição *</label>
            <input type="date" wire:model="measurement_date" class="form-control @error('measurement_date') is-invalid @enderror" required>
            @error('measurement_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="wysiwyg form-control" rows="2"></textarea>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
