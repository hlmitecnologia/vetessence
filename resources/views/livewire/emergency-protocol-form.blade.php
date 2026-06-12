<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Título *</label>
            <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" required>
            @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Espécie</label>
                    <input type="text" wire:model="species" class="form-control" placeholder="Ex: canina, felina">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Gravidade *</label>
                    <select wire:model="severity" class="form-control @error('severity') is-invalid @enderror" required>
                        <option value="stable">Estável</option>
                        <option value="urgent">Urgente</option>
                        <option value="critical">Crítico</option>
                    </select>
                    @error('severity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Categoria</label>
            <input type="text" wire:model="category" class="form-control" placeholder="Ex: Trauma, Intoxicação">
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea wire:model="description" class="wysiwyg form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label>Passos do Procedimento *</label>
            <textarea wire:model="procedure_steps" class="wysiwyg form-control @error('procedure_steps') is-invalid @enderror" rows="4" required></textarea>
            @error('procedure_steps') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Medicações</label>
            <textarea wire:model="medications" class="wysiwyg form-control" rows="2" maxlength="500"></textarea>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="epIsActive">
                <label class="custom-control-label" for="epIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
