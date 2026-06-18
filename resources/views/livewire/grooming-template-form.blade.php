<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Espécie</label>
            <input type="text" wire:model="species" class="form-control">
        </div>

        <div class="form-group">
            <label>Raça</label>
            <input type="text" wire:model="breed" class="form-control">
        </div>

        <div class="form-group">
            <label>Porte</label>
            <input type="text" wire:model="size" class="form-control">
        </div>

        <div class="form-group">
            <label>Serviços (JSON)</label>
            <textarea wire:model="services" class="wysiwyg form-control @error('services') is-invalid @enderror" rows="2"></textarea>
            @error('services') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Preço *</label>
                    <input type="number" step="0.01" min="0" wire:model="price" class="form-control @error('price') is-invalid @enderror" required>
                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Duração (min) *</label>
                    <input type="number" min="1" wire:model="estimated_minutes" class="form-control @error('estimated_minutes') is-invalid @enderror" required>
                    @error('estimated_minutes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="2"></textarea>
            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="groomingTemplateIsActive">
                <label class="custom-control-label" for="groomingTemplateIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
