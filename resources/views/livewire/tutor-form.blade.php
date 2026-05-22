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
                    <label>CPF *</label>
                    <input type="text" wire:model="cpf" class="form-control @error('cpf') is-invalid @enderror" required>
                    @error('cpf') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Telefone *</label>
                    <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>E-mail *</label>
            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Endereço</label>
            <input type="text" wire:model="address" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" wire:model="city" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>UF</label>
                    <input type="text" wire:model="state" class="form-control" maxlength="2" placeholder="SP">
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" onclick="closeTutorOverlay()">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
