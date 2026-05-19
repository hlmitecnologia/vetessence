@extends('layouts.adminlte', ['title' => 'Novo Plano de Tratamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Plano de Tratamento</h3>
        <div class="card-tools">
            <a href="{{ route('treatment-plans.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('treatment-plans.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tutor_id">Tutor</label>
                        <x-tom-select name="tutor_id" id="tutor_id" :value="old('tutor_id')">
                            @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('tutor_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_id">Veterinário *</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('vet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Título do Plano</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Ex: Plano Cirúrgico">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <hr>
            <h5>Itens do Plano</h5>
            <div id="plan-items-container">
                <div class="plan-item-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Descrição *</label>
                                <input type="text" name="items[0][description]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="items[0][category]" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach(['Procedimento', 'Medicação', 'Exame', 'Internação', 'Material', 'Outros'] as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control item-quantity" value="1" onchange="calculateRowTotal(this)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Valor Unitário (R$)</label>
                                <input type="number" step="0.01" name="items[0][unit_price]" class="form-control item-unit-price" value="0.00" onchange="calculateRowTotal(this)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Total (R$)</label>
                                <input type="number" step="0.01" name="items[0][total]" class="form-control item-total" value="0.00" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="items[0][notes]" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item-row" onclick="this.closest('.plan-item-row').remove(); updateTotals();">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-item-row" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Item
            </button>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="total_estimated">Total Estimado (R$)</label>
                        <input type="number" step="0.01" name="total_estimated" id="total_estimated" class="form-control" value="{{ old('total_estimated', '0.00') }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_notes">Observações do Veterinário</label>
                        <textarea name="vet_notes" id="vet_notes" rows="2" class="form-control">{{ old('vet_notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="form-group mb-0">
                <label>Status</label>
                <div class="d-flex align-items-center">
                    <div class="custom-control custom-radio mr-4">
                        <input type="radio" name="status" id="status_draft" value="draft" class="custom-control-input" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_draft">Rascunho</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="status" id="status_pending" value="pending_approval" class="custom-control-input" {{ old('status') == 'pending_approval' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_pending">Enviar para Aprovação</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Plano
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

document.getElementById('add-item-row').addEventListener('click', function() {
    const container = document.getElementById('plan-items-container');
    const template = container.querySelector('.plan-item-row').cloneNode(true);
    template.querySelectorAll('input, select').forEach(function(el) {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
        if (el.type !== 'hidden') el.value = '';
    });
    template.querySelector('.item-quantity').value = 1;
    template.querySelector('.item-unit-price').value = '0.00';
    template.querySelector('.item-total').value = '0.00';
    container.appendChild(template);
    itemIndex++;
});

function calculateRowTotal(el) {
    const row = el.closest('.plan-item-row');
    const qty = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-unit-price').value) || 0;
    row.querySelector('.item-total').value = (qty * price).toFixed(2);
    updateTotals();
}

function updateTotals() {
    let total = 0;
    document.querySelectorAll('.item-total').forEach(function(el) {
        total += parseFloat(el.value) || 0;
    });
    document.getElementById('total_estimated').value = total.toFixed(2);
}
</script>
@endpush
