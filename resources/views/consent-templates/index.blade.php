@extends('layouts.adminlte', ['title' => 'Modelos de Termos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modelos de Termos de Consentimento</h3>
        <div class="card-tools">
            <a href="{{ route('consent-templates.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Modelo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($templates->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Ativo</th>
                    <th>Usado em</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>{{ $template->category ?? '-' }}</td>
                    <td class="text-truncate" style="max-width: 250px;">{{ $template->description ?? '-' }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>{{ $template->consentForms->count() }}</td>
                    <td>
                        <a href="{{ route('consent-templates.edit', $template) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('consent-templates.destroy', $template) }}" method="POST" class="d-inline">
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
        <p class="text-center text-muted">Nenhum modelo de termo encontrado.</p>
        @endif
    </div>
</div>
@endsection
