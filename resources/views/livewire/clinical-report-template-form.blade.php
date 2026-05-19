<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Slug</label>
            <input type="text" wire:model="slug" class="form-control">
        </div>

        <div class="form-group">
            <label>Espécie</label>
            <select wire:model="species" class="form-control">
                <option value="">Todas</option>
                <option value="canine">Canina</option>
                <option value="feline">Felina</option>
                <option value="equine">Equina</option>
                <option value="bovine">Bovina</option>
                <option value="other">Outras</option>
            </select>
        </div>

        <div class="form-group">
            <label>Especialidade</label>
            <input type="text" wire:model="specialty" class="form-control">
        </div>

        <div class="form-group">
            <label>Categoria</label>
            <input type="text" wire:model="category" class="form-control">
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea wire:model="description" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label>Conteúdo *</label>
            <textarea wire:model="content" class="form-control @error('content') is-invalid @enderror" rows="5" required></textarea>
            @error('content') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="clinicReportTemplateIsActive">
                <label class="custom-control-label" for="clinicReportTemplateIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
