<div>
    @php $canSecurity = auth()->user()?->can('users.create') @endphp

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

        @if($canSecurity)
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
        @else
            @unless($userId)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    O funcionário receberá um e-mail para definir a própria senha.
                </div>
            @endunless
        @endif

        @if($canSecurity)
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Perfil</label>
                    <x-tom-select wire="role_id" :value="$role_id">
                        <option value="">Nenhum</option>
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
        @endif

        <hr>
        <h6>Dados Funcionais</h6>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Departamento</label>
                    <x-tom-select wire="department_id" :value="$department_id">
                        <option value="">Nenhum</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Cargo</label>
                    <x-tom-select wire="position_id" :value="$position_id">
                        <option value="">Nenhum</option>
                        @foreach($positions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data de Admissão</label>
                    <input type="date" wire:model="hire_date" class="form-control @error('hire_date') is-invalid @enderror">
                    @error('hire_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipo de Contrato</label>
                    <x-tom-select wire="contract_type" :value="$contract_type">
                        <option value="">Nenhum</option>
                        @foreach($contractTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" wire:model="is_active" class="custom-control-input" id="empIsActive">
                        <label class="custom-control-label" for="empIsActive">Ativo</label>
                    </div>
                </div>
            </div>
            @if($canSecurity)
            <div class="col-md-6">
                <div class="form-group">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" wire:model="is_veterinarian" class="custom-control-input" id="empIsVet">
                        <label class="custom-control-label" for="empIsVet">Veterinário</label>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
