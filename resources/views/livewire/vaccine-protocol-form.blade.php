<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Espécie *</label>
                    <select wire:model="species" class="form-control @error('species') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach(config('species') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('species') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome da Vacina *</label>
                    <input type="text" wire:model="vaccine_name" class="form-control @error('vaccine_name') is-invalid @enderror" required>
                    @error('vaccine_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Idade Início (semanas)</label>
                    <input type="number" wire:model="age_start_weeks" class="form-control" min="0">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Idade Fim (semanas)</label>
                    <input type="number" wire:model="age_end_weeks" class="form-control" min="0">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Dose Número</label>
                    <input type="number" wire:model="dose_number" class="form-control" min="1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Reforço (meses)</label>
                    <input type="number" wire:model="booster_interval_months" class="form-control" min="1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" wire:model="is_initial" class="custom-control-input" id="vpIsInitial">
                        <label class="custom-control-label" for="vpIsInitial">Série Inicial</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" wire:model="is_core" class="custom-control-input" id="vpIsCore">
                        <label class="custom-control-label" for="vpIsCore">Vacina Essencial</label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" wire:model="is_active" class="custom-control-input" id="vpIsActive">
                        <label class="custom-control-label" for="vpIsActive">Ativo</label>
                    </div>
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
