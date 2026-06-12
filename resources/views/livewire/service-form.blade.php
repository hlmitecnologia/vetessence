<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Categoria</label>
                    <x-tom-select wire="category_id" :value="$category_id">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Duração (min)</label>
                    <input type="number" wire:model="duration" class="form-control" min="1">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Preço *</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                <input type="number" wire:model="price" class="form-control @error('price') is-invalid @enderror" step="0.01" min="0" required>
            </div>
            @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea wire:model="description" class="wysiwyg form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="svcIsActive">
                <label class="custom-control-label" for="svcIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
