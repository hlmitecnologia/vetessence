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
                    <label>CNPJ</label>
                    <input type="text" wire:model="cnpj" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Inscrição Estadual</label>
                    <input type="text" wire:model="ie" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" wire:model="phone" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" wire:model="email" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Unidade</label>
            <select wire:model="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                <option value="">Todas as unidades</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Endereço</label>
            <input type="text" wire:model="address" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" wire:model="city" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>UF</label>
                    <input type="text" wire:model="state" class="form-control" maxlength="2">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Contato</label>
                    <input type="text" wire:model="contact" class="form-control">
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
