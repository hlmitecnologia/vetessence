<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tipo *</label>
            <select wire:model="type" class="form-control @error('type') is-invalid @enderror" required>
                <option value="service">Serviço</option>
                <option value="product">Produto</option>
                <option value="vaccine">Vacina</option>
            </select>
            @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea wire:model="description" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label>Categoria Pai</label>
            <select wire:model="parent_id" class="form-control">
                <option value="">Nenhuma (raiz)</option>
                @foreach($parentCategories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Salvar
            </button>
        </div>
    </form>
</div>
