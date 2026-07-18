@extends('layouts.adminlte', ['title' => 'Faturas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Faturas</h3>
        <div class="card-tools">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($invoices->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº Fatura</th>
                    <th>Tutor</th>
                    <th>Pet</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>NFSe</th>
                    <th>NFC-e</th>
                    <th style="width: 180px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td><strong>{{ $inv->invoice_number }}</strong></td>
                    <td>{{ $inv->tutor->name ?? '-' }}</td>
                    <td>{{ $inv->pet->name ?? '-' }}</td>
                    <td>R$ {{ number_format($inv->total, 2, ',', '.') }}</td>
                    <td data-order="{{ $inv->due_date->format('Y-m-d') }}">{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $statusColors = ['pending' => 'badge-warning', 'paid' => 'badge-success', 'overdue' => 'badge-danger', 'cancelled' => 'badge-secondary'];
                            $statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'overdue' => 'Vencido', 'cancelled' => 'Cancelado'];
                        @endphp
                        <span class="badge {{ $statusColors[$inv->status] ?? 'badge-secondary' }}">{{ $statusLabels[$inv->status] ?? $inv->status }}</span>
                    </td>
                    <td>
                        @php
                            $nfseLabels = ['none' => '—', 'pending' => 'Pendente', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                            $nfseColors = ['none' => '', 'pending' => 'badge-warning', 'issued' => 'badge-success', 'cancelled' => 'badge-secondary'];
                        @endphp
                        @if($inv->nfse_status && $inv->nfse_status !== 'none')
                        <span class="badge {{ $nfseColors[$inv->nfse_status] ?? '' }}">{{ $nfseLabels[$inv->nfse_status] ?? $inv->nfse_status }}</span>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @php
                            $nfeLabels = ['none' => '—', 'issuing' => 'Pendente', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                            $nfeColors = ['none' => '', 'issuing' => 'badge-warning', 'issued' => 'badge-success', 'cancelled' => 'badge-secondary'];
                        @endphp
                        @if($inv->nfe_status && $inv->nfe_status !== 'none')
                        <span class="badge {{ $nfeColors[$inv->nfe_status] ?? '' }}">{{ $nfeLabels[$inv->nfe_status] ?? $inv->nfe_status }}</span>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($inv->status !== 'paid')
                        <form action="{{ route('invoices.cancel', $inv) }}" method="POST" class="d-inline" data-confirm="Cancelar esta fatura?">
                            @csrf
                            <button type="submit" class="btn btn-action btn-danger" title="Cancelar">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif
                        @if(auth()->user()->can('nfse.emit') || auth()->user()->can('nfe.emit'))
                        @if($inv->status === 'paid' && ($inv->has_services > 0 || $inv->has_products > 0) && ($inv->nfse_status === 'none' || $inv->nfe_status === 'none'))
                        <button type="button" class="btn btn-action btn-success" title="Emitir Nota Fiscal" onclick="emitirNotaFiscalIndex({{ $inv->id }}, this)">
                            <i class="fas fa-file-invoice"></i>
                        </button>
                        @endif
                        @endif
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

{{-- Loading overlay --}}
<div id="nfce-loading-overlay" class="d-none" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
    <div class="bg-white rounded p-4 text-center shadow-lg">
        <div class="spinner-border text-success mb-3" role="status" style="width:3rem;height:3rem;">
            <span class="sr-only">Emitindo...</span>
        </div>
        <h5 class="mb-1">Emitindo Nota Fiscal</h5>
        <p id="nfce-loading-status" class="text-muted mb-0">Aguardando autorização da SEFAZ...</p>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    var table = $('table.table-bordered').first();
    if (table.length) {
        // Destroi o DataTable criado pelo auto-init (que ordena por coluna 0)
        // e recria com order:[] para preservar a ordenação do servidor
        if (table.hasClass('dataTable')) {
            table.DataTable().destroy();
        }
        table.DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            order: [],
            columns: Array.from({length: table.find('thead th').length}, function() { return {}; }),
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
            language: {
                sEmptyTable: 'Nenhum registro encontrado',
                sInfo: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
                sInfoEmpty: 'Mostrando 0 até 0 de 0 registros',
                sInfoFiltered: '(Filtrados de _MAX_ registros)',
                sLengthMenu: '_MENU_ registros por página',
                sLoadingRecords: 'Carregando...',
                sProcessing: 'Processando...',
                sSearch: 'Pesquisar:',
                sSearchPlaceholder: 'Buscar...',
                sZeroRecords: 'Nenhum registro encontrado',
                oPaginate: {
                    sNext: 'Próximo',
                    sPrevious: 'Anterior',
                    sFirst: 'Primeiro',
                    sLast: 'Último'
                },
                oAria: {
                    sSortAscending: ': Ordenar colunas de forma ascendente',
                    sSortDescending: ': Ordenar colunas de forma descendente'
                }
            }
        });
    }
});

function emitirNotaFiscalIndex(invoiceId, btn) {
    var overlay = document.getElementById('nfce-loading-overlay');
    overlay.classList.remove('d-none');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('/invoices/' + invoiceId + '/emitir-nota-fiscal', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.issuing) {
            document.getElementById('nfce-loading-status').textContent = 'NFC-e enviada para SEFAZ. Aguardando autorização...';
            pollNfceStatusIndex(invoiceId, data.nfceInvoiceId, overlay);
        } else if (data.success) {
            window.location.reload();
        } else {
            overlay.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-invoice"></i>';
            alert(data.message || 'Erro ao emitir nota fiscal.');
        }
    })
    .catch(function() {
        overlay.classList.add('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-invoice"></i>';
        alert('Erro de comunicação com o servidor.');
    });
}

function pollNfceStatusIndex(invoiceId, apiInvoiceId, overlay) {
    if (!overlay) overlay = document.getElementById('nfce-loading-overlay');

    function poll() {
        fetch('/invoices/' + invoiceId + '/nfce-status/' + (apiInvoiceId || ''), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.issued) {
                document.getElementById('nfce-loading-status').textContent = 'NFC-e autorizada!';
                setTimeout(function() { window.location.reload(); }, 1000);
            } else if (data.issuing) {
                document.getElementById('nfce-loading-status').textContent = data.status || 'Aguardando autorização da SEFAZ...';
                setTimeout(poll, 3000);
            } else {
                overlay.classList.add('d-none');
                alert(data.message || 'Erro ao consultar NFC-e.');
            }
        })
        .catch(function() {
            document.getElementById('nfce-loading-status').textContent = 'Erro de conexão. Tentando novamente...';
            setTimeout(poll, 5000);
        });
    }

    setTimeout(poll, 2000);
}
</script>
@endpush