@extends('layouts.adminlte', ['title' => 'Editar Pedido de Laboratório'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Pedido - {{ $order->order_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('laboratory-orders.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('laboratory-orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Nº do Pedido</label>
                        <p class="form-control-plaintext">{{ $order->order_number }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Pet</label>
                        <p class="form-control-plaintext">{{ $order->pet->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Veterinário</label>
                        <p class="form-control-plaintext">{{ $order->vet->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Laboratório</label>
                        <p class="form-control-plaintext">{{ $order->lab_name ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Data do Pedido</label>
                        <p class="form-control-plaintext">{{ $order->order_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach(['requested' => 'Solicitado', 'collected' => 'Coletado', 'in_analysis' => 'Em Análise', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $order->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="result_date">Data do Resultado</label>
                        <input type="date" name="result_date" id="result_date"
                               class="form-control @error('result_date') is-invalid @enderror"
                               value="{{ old('result_date', $order->result_date?->format('Y-m-d') ?? '') }}">
                        @error('result_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            @if($order->notes)
            <div class="form-group">
                <label>Observações do Pedido</label>
                <p class="form-control-plaintext">{!! $order->notes !!}</p>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleResultDateRequired() {
        const status = document.getElementById('status').value;
        const resultDate = document.getElementById('result_date');
        if (status === 'completed') {
            resultDate.setAttribute('required', 'required');
        } else {
            resultDate.removeAttribute('required');
        }
    }
    document.getElementById('status').addEventListener('change', toggleResultDateRequired);
    toggleResultDateRequired();
</script>
@endpush
@endsection
