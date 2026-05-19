<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Princípio Ativo</label>
                    <input type="text" wire:model="active_ingredient" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Lista/Controle *</label>
                    <select wire:model="schedule" class="form-control @error('schedule') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="A3">A3</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                        <option value="C1">C1</option>
                    </select>
                    @error('schedule') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Unidade *</label>
                    <input type="text" wire:model="unit" class="form-control @error('unit') is-invalid @enderror" placeholder="Ex: mg, ml, comp" required>
                    @error('unit') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Registro ANVISA</label>
                    <input type="text" wire:model="anvisa_register" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Estoque Mínimo</label>
                    <input type="number" wire:model="min_stock" class="form-control" step="0.01" min="0">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Estoque Atual *</label>
                    <input type="number" wire:model="current_stock" class="form-control @error('current_stock') is-invalid @enderror" step="0.01" min="0" required>
                    @error('current_stock') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" wire:model="is_active" class="custom-control-input" id="csIsActive">
                        <label class="custom-control-label" for="csIsActive">Ativo</label>
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
