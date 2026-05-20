@extends('layouts.adminlte', ['title' => 'Produtos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Produtos</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Produto</th>
                    <th>Lote</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $prod)
                <tr>
                    <td><code>{{ $prod->sku }}</code></td>
                    <td>
                        <strong>{{ $prod->name }}</strong>
                        @if($prod->isLowStock)
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Baixo</span>
                        @endif
                    </td>
                    <td>{{ $prod->batch_number ?? $prod->lot_number ?? '-' }}</td>
                    <td class="{{ $prod->isLowStock ? 'text-danger font-weight-bold' : '' }}">{{ $prod->stock }}</td>
                    <td>R$ {{ number_format($prod->sale_price, 2, ',', '.') }}</td>
                    <td>
                        @if($prod->expiration_date)
                            @php $diff = now()->diffInDays($prod->expiration_date, false); @endphp
                            <span class="badge {{ $diff <= 0 ? 'badge-danger' : ($diff <= 30 ? 'badge-warning' : 'badge-secondary') }}">
                                {{ $prod->expiration_date->format('d/m/Y') }}
                                @if($diff <= 0) <i class="fas fa-times-circle"></i> Vencido
                                @elseif($diff <= 30) <i class="fas fa-clock"></i> {{ $diff }}d
                                @endif
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($prod->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $prod) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $prod->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Novo Produto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('product-form', key('product-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#productModal').modal('hide'); });
        Livewire.on('product-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('productModalTitle').textContent = 'Novo Produto';
        $('#productModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editProduct', { id: id });
        document.getElementById('productModalTitle').textContent = 'Editar Produto';
        $('#productModal').modal('show');
    }
</script>
@endpush
