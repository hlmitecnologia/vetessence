@extends('layouts.adminlte', ['title' => 'Integração de Equipamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $labEquipmentIntegration->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('lab-equipment-integrations.edit', $labEquipmentIntegration) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('lab-equipment-integrations.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Nome:</strong><p>{{ $labEquipmentIntegration->name }}</p></div>
            <div class="col-md-4"><strong>Tipo:</strong><p>{{ $labEquipmentIntegration->equipment_type }}</p></div>
            <div class="col-md-4"><strong>Protocolo:</strong><p>{{ strtoupper($labEquipmentIntegration->protocol) }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>URL:</strong><p>{{ $labEquipmentIntegration->endpoint_url ?? '-' }}</p></div>
            <div class="col-md-4"><strong>IP:</strong><p>{{ $labEquipmentIntegration->ip_address ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Porta:</strong><p>{{ $labEquipmentIntegration->port ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Status:</strong><p>@if($labEquipmentIntegration->is_active) <span class="badge badge-success">Ativo</span> @else <span class="badge badge-secondary">Inativo</span> @endif</p></div>
            <div class="col-md-4"><strong>Último Contato:</strong><p>{{ $labEquipmentIntegration->last_contact_at?->format('d/m/Y H:i:s') ?? 'Nunca' }}</p></div>
            <div class="col-md-4">
                <strong>Webhook (API):</strong>
                <p><code>{{ url('/api/v1/lab-equipment/' . $labEquipmentIntegration->id . '/receive') }}</code></p>
            </div>
        </div>
        @if($labEquipmentIntegration->notes)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Observações:</strong><p>{{ $labEquipmentIntegration->notes }}</p></div>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Resultados Recebidos ({{ $labEquipmentIntegration->results->count() }})</h3></div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Teste</th>
                    <th>Pet</th>
                    <th>Status</th>
                    <th>Recebido em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labEquipmentIntegration->results->sortByDesc('received_at') as $result)
                <tr>
                    <td>{{ $result->id }}</td>
                    <td>{{ $result->test_type }}</td>
                    <td>{{ $result->pet->name ?? 'N/A' }}</td>
                    <td>
                        @if($result->status == 'received') <span class="badge badge-info">Recebido</span>
                        @elseif($result->status == 'processed') <span class="badge badge-success">Processado</span>
                        @else <span class="badge badge-danger">Erro</span> @endif
                    </td>
                    <td>{{ $result->received_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Nenhum resultado recebido ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
