@extends('layouts.adminlte', ['title' => $role->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users mr-2"></i>Usuários com este perfil</h5>
                @if($role->users->count() > 0)
                <ul class="list-unstyled mb-0">
                    @foreach($role->users as $user)
                    <li><i class="fas fa-user text-muted mr-2"></i>{{ $user->name }}</li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">Nenhum usuário com este perfil.</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection
