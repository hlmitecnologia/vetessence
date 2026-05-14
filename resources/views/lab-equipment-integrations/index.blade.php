@extends('layouts.adminlte', ['title' => 'Integração de Equipamentos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Integração de Equipamentos de Laboratório</h3>
        <div class="card-tools">
            <a href="{{ route('lab-equipment-integrations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Integração
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($integrations->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Protocolo</th>
                    <th>Último Contato</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrations as $integration)
                <tr>
                    <td><strong>{{ $integration->name }}</strong></td>
                    <td>{{ $integration->equipment_type }}</td>
                    <td>{{ strtoupper($integration->protocol) }}</td>
                    <td>{{ optional($integration->last_contact_at)->format('d/m/Y H:i') ?? 'Nunca' }}</td>
                    <td>
                        @if($integration->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lab-equipment-integrations.show', $integration) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('lab-equipment-integrations.edit', $integration) }}" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $integrations->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhuma integração cadastrada.</p>
        @endif
    </div>
</div>
@endsection
