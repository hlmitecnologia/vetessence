@extends('layouts.adminlte', ['title' => 'Tutores'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tutores</h3>
        <div class="card-tools">
            <a href="{{ route('tutors.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($tutors->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Pets</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tutors as $tutor)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-circle d-flex align-items-center justify-content-center text-indigo-600 font-semibold">
                                {{ substr($tutor->name, 0, 1) }}
                            </div>
                            <div class="ml-2">
                                <div class="font-weight-bold">{{ $tutor->name }}</div>
                                <small class="text-muted">{{ $tutor->city ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $tutor->cpf }}</td>
                    <td>{{ $tutor->phone }}</td>
                    <td>{{ $tutor->email }}</td>
                    <td><span class="badge badge-primary">{{ $tutor->pets->count() ?? 0 }}</span></td>
                    <td>
                        <a href="{{ route('tutors.show', $tutor) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('tutors.edit', $tutor) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('tutors.destroy', $tutor) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection