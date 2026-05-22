@extends('layouts.adminlte', ['title' => 'Nova Fatura'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tutor *</label>
                                <x-tom-select name="tutor_id" :value="old('tutor_id', $tutor->id ?? '')" required>
                                    <option value="">Selecione...</option>
                                    @foreach($tutors as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vencimento *</label>
                                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required class="form-control">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-3"><i class="fas fa-list mr-2"></i>Itens</h5>
                    <hr>

                    <div id="items-container">
                        <div class="item-row row align-items-end mb-2 p-3 bg-light rounded">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="small text-muted">Descrição</label>
                                    <input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Descrição do item">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label class="small text-muted">Qtd</label>
                                    <input type="number" name="items[0][quantity]" value="1" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small text-muted">Valor Unit.</label>
                                    <input type="number" name="items[0][unit_price]" step="0.01" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="button" onclick="this.closest('.item-row').remove()" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="addItem()" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus mr-1"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Criar Fatura</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const html = `<div class="item-row row align-items-end mb-2 p-3 bg-light rounded">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="small text-muted">Descrição</label>
                <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" placeholder="Descrição">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-0">
                <label class="small text-muted">Qtd</label>
                <input type="number" name="items[${itemIndex}][quantity]" value="1" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-0">
                <label class="small text-muted">Valor Unit.</label>
                <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" onclick="this.closest('.item-row').remove()" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
        </div>
    </div>`;
    document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
    itemIndex++;
}
</script>
@endpush
@endsection
