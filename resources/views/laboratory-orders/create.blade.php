@extends('layouts.adminlte', ['title' => 'Novo Pedido de Laboratório'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Pedido de Laboratório</h3>
        <div class="card-tools">
            <a href="{{ route('laboratory-orders.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('laboratory-orders.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lab_name">Laboratório</label>
                        <input type="text" name="lab_name" id="lab_name" class="form-control" value="{{ old('lab_name') }}" placeholder="Ex: LabVet, Hovet">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order_date">Data do Pedido *</label>
                        <input type="date" name="order_date" id="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                        @error('order_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['requested' => 'Solicitado', 'collected' => 'Coletado', 'in_analysis' => 'Em Análise'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', 'requested') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Exames / Testes</h5>
            <div id="tests-container">
                <div class="test-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome do Exame *</label>
                                <input type="text" name="tests[0][test_name]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Código</label>
                                <input type="text" name="tests[0][test_code]" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Valor de Referência</label>
                                <input type="text" name="tests[0][reference_range]" class="form-control" placeholder="Ex: 35-55">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Unidade</label>
                                <input type="text" name="tests[0][unit]" class="form-control" placeholder="Ex: g/dL">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.test-row').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="tests[0][observations]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-test-row" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Exame
            </button>

            <div class="form-group">
                <label for="notes">Observações do Pedido</label>
                <textarea name="notes" id="notes" rows="2" class="wysiwyg form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Pedido
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let testIndex = 1;
document.getElementById('add-test-row').addEventListener('click', function() {
    const container = document.getElementById('tests-container');
    const template = container.querySelector('.test-row').cloneNode(true);
    template.querySelectorAll('input').forEach(function(el) {
        el.name = el.name.replace(/tests\[\d+\]/, 'tests[' + testIndex + ']');
        if (el.type !== 'hidden') el.value = '';
    });
    container.appendChild(template);
    testIndex++;
});
</script>
@endpush
