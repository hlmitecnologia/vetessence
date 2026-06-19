@extends('layouts.adminlte', ['title' => 'Novo Perfil'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required class="form-control @error('slug') is-invalid @enderror" placeholder="ex: veterinario">
                        @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="description" rows="2" class="wysiwyg form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <hr>
                    <h5><i class="fas fa-shield-alt mr-1"></i> Permissões</h5>
                    <p class="text-muted small">Marque as permissões que este perfil terá acesso.</p>

                    @foreach($groupedPermissions as $group => $perms)
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="text-uppercase small text-secondary">{{ $group }}</strong>
                            <label class="ml-3 mb-0 small">
                                <input type="checkbox" class="check-all-group" data-group="{{ $group }}"> Marcar todos
                            </label>
                        </div>
                        <div class="row">
                            @foreach($perms as $perm)
                            <div class="col-md-3 col-sm-4 col-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        class="custom-control-input perm-{{ $group }}"
                                        id="perm-{{ $perm->id }}"
                                        @checked(in_array($perm->id, old('permissions', [])))>
                                    <label class="custom-control-label small" for="perm-{{ $perm->id }}">
                                        {{ $perm->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @error('permissions')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.check-all-group').forEach(function(el) {
        el.addEventListener('change', function() {
            var group = this.dataset.group;
            document.querySelectorAll('.perm-' + group).forEach(function(cb) {
                cb.checked = el.checked;
            });
        });
    });
</script>
@endpush
