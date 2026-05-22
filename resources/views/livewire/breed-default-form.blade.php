<div>
    <form wire:submit.prevent="save">
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

        <div class="form-group">
            <label>Raça *</label>
            <input type="text" wire:model="breed" class="form-control @error('breed') is-invalid @enderror" required>
            @error('breed') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Porte</label>
            <input type="text" wire:model="size" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Peso Mínimo (kg)</label>
                    <input type="number" step="0.01" min="0" wire:model="avg_weight_min" class="form-control @error('avg_weight_min') is-invalid @enderror">
                    @error('avg_weight_min') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Peso Máximo (kg)</label>
                    <input type="number" step="0.01" min="0" wire:model="avg_weight_max" class="form-control @error('avg_weight_max') is-invalid @enderror">
                    @error('avg_weight_max') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Expectativa de Vida Mín (anos)</label>
                    <input type="number" min="0" wire:model="avg_lifespan_min" class="form-control @error('avg_lifespan_min') is-invalid @enderror">
                    @error('avg_lifespan_min') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Expectativa de Vida Máx (anos)</label>
                    <input type="number" min="0" wire:model="avg_lifespan_max" class="form-control @error('avg_lifespan_max') is-invalid @enderror">
                    @error('avg_lifespan_max') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Temperamento</label>
            <input type="text" wire:model="temperament" class="form-control">
        </div>

        <div class="form-group">
            <label>Predisposições</label>
            <textarea wire:model="predispositions" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="breedDefaultIsActive">
                <label class="custom-control-label" for="breedDefaultIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
