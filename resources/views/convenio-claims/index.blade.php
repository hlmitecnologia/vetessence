@extends('layouts.adminlte', ['title' => 'Solicitações de Reembolso'])
@section('content')
    <div class="card">
        <div class="card-header">
            <button onclick="openCreateModal()" class="btn btn-primary">Nova Solicitação</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Nº</th><th>Convênio</th><th>Pet</th><th>Solicitado</th><th>Aprovado</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                @foreach($claims as $c)
                    <tr>
                        <td>{{ $c->claim_number }}</td>
                        <td>{{ optional($c->convenioPet->convenio)->name ?? '-' }}</td>
                        <td>{{ optional($c->convenioPet->pet)->name ?? '-' }}</td>
                        <td>R$ {{ number_format($c->amount_requested, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($c->amount_approved ?? 0, 2, ',', '.') }}</td>
                        @php $statusLabels = ['draft' => 'Rascunho', 'filed' => 'Protocolado', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado']; @endphp
                        <td>{{ $statusLabels[$c->status] ?? $c->status }}</td>
                        <td><a href="{{ route('convenio-claims.show', $c) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- ConvenioClaim Modal -->
<div class="modal fade" id="convenioClaimModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convenioClaimModalTitle">Nova Solicitação</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('convenio-claim-form', key('convenio-claim-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#convenioClaimModal').modal('hide'); });
        Livewire.on('convenio-claim-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('convenioClaimModalTitle').textContent = 'Nova Solicitação';
        $('#convenioClaimModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editConvenioClaim', { id: id });
        document.getElementById('convenioClaimModalTitle').textContent = 'Editar Solicitação';
        $('#convenioClaimModal').modal('show');
    }
</script>
@endpush
