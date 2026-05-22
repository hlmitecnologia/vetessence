@extends('layouts.adminlte', ['title' => $user->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mr-3" style="width: 64px; height: 64px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white);">
                        <span style="font-size: 1.5rem; font-weight: bold; color: var(--brand-primary, #455e36);">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Email</small>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Telefone</small>
                        <p>{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Perfil</small>
                        <p>{{ $user->role->name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Criado em</small>
                        <p>{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection
