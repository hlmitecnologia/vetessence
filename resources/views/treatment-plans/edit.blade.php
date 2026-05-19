@extends('layouts.adminlte', ['title' => 'Editar Plano de Tratamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Plano - {{ $plan->plan_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('treatment-plans.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('treatment-plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nº do Plano</label>
                        <input type="text" class="form-control" value="{{ $plan->plan_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id', $plan->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $plan->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tutor_id">Tutor</label>
                        <x-tom-select name="tutor_id" id="tutor_id" :value="old('tutor_id', $plan->tutor_id)">
                            @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}" {{ old('tutor_id', $plan->tutor_id) == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
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
                        <label for="title">Título</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $plan->title) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['draft' => 'Rascunho', 'pending_approval' => 'Aguardando Aprovação', 'approved' => 'Aprovado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $plan->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" rows="2" class="form-control">{{ old('description', $plan->description) }}</textarea>
            </div>

            <hr>
            <h5>Itens do Plano</h5>
            <div id="plan-items-container">
                @foreach($plan->items as $i => $item)
                <div class="plan-item-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Descrição *</label>
                                <input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item->description }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="items[{{ $i }}][category]" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach(['Procedimento', 'Medicação', 'Exame', 'Internação', 'Material', 'Outros'] as $cat)
                                        <option value="{{ $cat }}" {{ $item->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity }}" onchange="calculateRowTotal(this)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Valor Unitário (R$)</label>
                                <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control item-unit-price" value="{{ $item->unit_price }}" onchange="calculateRowTotal(this)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Total (R$)</label>
                                <input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control item-total" value="{{ $item->total }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="items[{{ $i }}][notes]" class="form-control" value="{{ $item->notes }}">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" name="items[{{ $i }}][is_authorized]" id="item_auth_{{ $i }}" class="custom-control-input" value="1" {{ $item->is_authorized ? 'checked' : '' }}>
                                <label class="custom-control-label" for="item_auth_{{ $i }}">Autorizado</label>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-item-row" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Item
            </button>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="total_estimated">Total Estimado (R$)</label>
                        <input type="number" step="0.01" name="total_estimated" id="total_estimated" class="form-control" value="{{ old('total_estimated', $plan->total_estimated) }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_notes">Observações do Veterinário</label>
                        <textarea name="vet_notes" id="vet_notes" rows="2" class="form-control">{{ old('vet_notes', $plan->vet_notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = {{ $plan->items->count() }};

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

updateTotals();
</script>
@endpush
