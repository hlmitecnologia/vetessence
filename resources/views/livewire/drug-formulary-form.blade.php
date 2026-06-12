<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fármaco *</label>
                    <input type="text" wire:model="drug" class="form-control @error('drug') is-invalid @enderror" required>
                    @error('drug') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Espécie *</label>
                    <select wire:model="species" class="form-control @error('species') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="canina">Canina</option>
                        <option value="felina">Felina</option>
                        <option value="equina">Equina</option>
                        <option value="bovina">Bovina</option>
                    </select>
                    @error('species') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Dosagem (mg/kg) *</label>
                    <input type="number" wire:model="dosage_mg_kg" class="form-control @error('dosage_mg_kg') is-invalid @enderror" step="0.01" min="0.01" required>
                    @error('dosage_mg_kg') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Dose Máxima (mg)</label>
                    <input type="number" wire:model="max_dose" class="form-control" step="0.01" min="0">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Via de Administração</label>
            <select wire:model="route" class="form-control">
                <option value="">Selecione...</option>
                <option value="IV">IV</option>
                <option value="IM">IM</option>
                <option value="SC">SC</option>
                <option value="VO">VO</option>
                <option value="IO">IO</option>
                <option value="TP">Tópico</option>
            </select>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="wysiwyg form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="dfIsActive">
                <label class="custom-control-label" for="dfIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
