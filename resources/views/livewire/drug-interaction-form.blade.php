<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Medicamento A *</label>
                    <input type="text" wire:model="drug_a" class="form-control @error('drug_a') is-invalid @enderror" required>
                    @error('drug_a') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Medicamento B *</label>
                    <input type="text" wire:model="drug_b" class="form-control @error('drug_b') is-invalid @enderror" required>
                    @error('drug_b') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Severidade *</label>
            <select wire:model="severity" class="form-control @error('severity') is-invalid @enderror" required>
                <option value="contraindicated">Contraindicada</option>
                <option value="caution">Precaução</option>
                <option value="minor">Menor</option>
            </select>
            @error('severity') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Descrição *</label>
            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3" required></textarea>
            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Mecanismo</label>
            <input type="text" wire:model="mechanism" class="form-control">
        </div>

        <div class="form-group">
            <label>Manejo</label>
            <textarea wire:model="management" class="form-control" rows="2"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fonte</label>
                    <input type="text" wire:model="source" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Categoria</label>
                    <input type="text" wire:model="category" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="drugInteractionIsActive">
                <label class="custom-control-label" for="drugInteractionIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
