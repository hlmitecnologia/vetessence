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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nº do Pedido</label>
                        <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id', $order->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $order->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['requested' => 'Solicitado', 'collected' => 'Coletado', 'in_analysis' => 'Em Análise', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $order->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lab_name">Laboratório</label>
                        <input type="text" name="lab_name" id="lab_name" class="form-control" value="{{ old('lab_name', $order->lab_name) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="order_date">Data do Pedido *</label>
                        <input type="date" name="order_date" id="order_date" class="form-control" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="result_date">Data do Resultado</label>
                        <input type="date" name="result_date" id="result_date" class="form-control" value="{{ old('result_date', $order->result_date ? $order->result_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>

            <hr>
            <h5>Exames / Testes</h5>
            <div id="tests-container">
                @foreach($order->tests as $i => $test)
                <div class="test-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome do Exame *</label>
                                <input type="text" name="tests[{{ $i }}][test_name]" class="form-control" value="{{ $test->test_name }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Código</label>
                                <input type="text" name="tests[{{ $i }}][test_code]" class="form-control" value="{{ $test->test_code }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Resultado</label>
                                <input type="text" name="tests[{{ $i }}][result]" class="form-control" value="{{ $test->result }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Valor de Referência</label>
                                <input type="text" name="tests[{{ $i }}][reference_range]" class="form-control" value="{{ $test->reference_range }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Unidade</label>
                                <input type="text" name="tests[{{ $i }}][unit]" class="form-control" value="{{ $test->unit }}">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.test-row').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="tests[{{ $i }}][observations]" class="form-control" value="{{ $test->observations }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mt-4 pt-2">
                                <input type="checkbox" name="tests[{{ $i }}][is_abnormal]" id="test_abnormal_{{ $i }}" class="custom-control-input" value="1" {{ $test->is_abnormal ? 'checked' : '' }}>
                                <label class="custom-control-label" for="test_abnormal_{{ $i }}">Resultado Anormal</label>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-test-row" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Exame
            </button>

            <div class="form-group">
                <label for="notes">Observações do Pedido</label>
                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $order->notes) }}</textarea>
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
let testIndex = {{ $order->tests->count() }};
document.getElementById('add-test-row').addEventListener('click', function() {
    const container = document.getElementById('tests-container');
    const template = container.querySelector('.test-row').cloneNode(true);
    template.querySelectorAll('input, select').forEach(function(el) {
        el.name = el.name.replace(/tests\[\d+\]/, 'tests[' + testIndex + ']');
        if (el.type !== 'hidden') el.value = '';
    });
    container.appendChild(template);
    testIndex++;
});
</script>
@endpush
