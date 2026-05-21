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
                    <label>E-mail *</label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" wire:model="phone" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Senha {{ $userId ? '(deixe em branco para manter)' : '*' }}</label>
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" {{ $userId ? '' : 'required' }}>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                    @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Perfil</label>
                    <x-tom-select wire="role_id" :value="$role_id">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Unidade</label>
                    <x-tom-select wire="branch_id" :value="$branch_id">
                        <option value="">Todas as unidades</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" wire:model="is_active" class="custom-control-input" id="userIsActive">
                        <label class="custom-control-label" for="userIsActive">Ativo</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
