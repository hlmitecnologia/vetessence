<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Convênio *</label>
            <select wire:model="convenio_id" class="form-control @error('convenio_id') is-invalid @enderror">
                <option value="">Selecione...</option>
                @foreach ($convenios as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->plan_name }})</option>
                @endforeach
            </select>
            @error('convenio_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nº Apólice</label>
                    <input type="text" wire:model="policy_number" class="form-control" placeholder="Opcional">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Desconto (%) *</label>
                    <input type="number" wire:model="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror" min="0" max="100" step="0.01">
                    @error('discount_percent') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data Início</label>
                    <input type="date" wire:model="start_date" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data Fim</label>
                    <input type="date" wire:model="end_date" class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Pets Cobertos *</label>
            <div class="row">
                @forelse ($pets as $pet)
                    <div class="col-md-4 col-sm-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" wire:model="selectedPets" value="{{ $pet->id }}" class="custom-control-input" id="pet_{{ $pet->id }}">
                            <label class="custom-control-label" for="pet_{{ $pet->id }}">{{ $pet->name }} ({{ $pet->species }})</label>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted mb-0">Nenhum pet encontrado para este tutor.</p>
                    </div>
                @endforelse
            </div>
            @error('selectedPets') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="subIsActive">
                <label class="custom-control-label" for="subIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
