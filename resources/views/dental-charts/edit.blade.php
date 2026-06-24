@extends('layouts.adminlte', ['title' => 'Editar Odontograma'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Odontograma - {{ $chart->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('dental-charts.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('dental-charts.update', $chart) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Paciente *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id', $chart->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $chart->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vet_id">Veterinário *</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id', $chart->vet_id)" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id', $chart->vet_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="examination_date">Data do Exame *</label>
                        <input type="date" name="examination_date" id="examination_date" class="form-control" value="{{ old('examination_date', $chart->examination_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="procedure_type">Tipo de Procedimento *</label>
                        <select name="procedure_type" id="procedure_type" class="form-control" required>
                            @foreach(['cleaning' => 'Limpeza', 'extraction' => 'Extração', 'surgery' => 'Cirurgia', 'exam' => 'Exame', 'other' => 'Outro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('procedure_type', $chart->procedure_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tartar_index">Índice de Tártaro</label>
                        <select name="tartar_index" id="tartar_index" class="form-control">
                            <option value="">Selecione</option>
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}" {{ old('tartar_index', $chart->tartar_index) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="gingivitis_index">Índice de Gengivite</label>
                        <select name="gingivitis_index" id="gingivitis_index" class="form-control">
                            <option value="">Selecione</option>
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}" {{ old('gingivitis_index', $chart->gingivitis_index) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" name="halitosis" id="halitosis" class="custom-control-input" value="1" {{ old('halitosis', $chart->halitosis) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="halitosis">Halitose</label>
                </div>
            </div>

            <hr>
            <h5>Condições Dentárias</h5>
            <div id="conditions-container">
                @foreach($chart->conditions as $i => $condition)
                <div class="condition-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quadrante *</label>
                                <select name="conditions[{{ $i }}][quadrant]" class="form-control" required>
                                    <option value="">Q</option>
                                    @foreach(['1', '2', '3', '4'] as $q)
                                        <option value="{{ $q }}" {{ $condition->quadrant == $q ? 'selected' : '' }}>{{ $q }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nº do Dente *</label>
                                <select name="conditions[{{ $i }}][tooth_number]" class="form-control" required>
                                    <option value="">Dente</option>
                                    @foreach([101,102,103,104,105,106,107,108,109,110,201,202,203,204,205,206,207,208,209,210,301,302,303,304,305,306,307,308,309,310,401,402,403,404,405,406,407,408,409,410] as $t)
                                        <option value="{{ $t }}" {{ $condition->tooth_number == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Condição *</label>
                                <select name="conditions[{{ $i }}][condition]" class="form-control" required>
                                    <option value="">Selecione</option>
                                    @foreach(['Placa', 'Tártaro', 'Gengivite', 'Retração Gengival', 'Mobilidade', 'Fratura', 'Lesão de Reabsorção', 'Pulpite', 'Cárie', 'Odontólito', 'Persistência', 'Anomalia', 'Outros'] as $cond)
                                        <option value="{{ $cond }}" {{ $condition->condition == $cond ? 'selected' : '' }}>{{ $cond }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Severidade</label>
                                <select name="conditions[{{ $i }}][severity]" class="form-control">
                                    @for($s = 1; $s <= 5; $s++)
                                        <option value="{{ $s }}" {{ $condition->severity == $s ? 'selected' : '' }}>{{ $s }}/5</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="conditions[{{ $i }}][notes]" class="form-control" value="{!! $condition->notes !!}">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.condition-row').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-condition" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Condição
            </button>

            <div class="form-group">
                <label for="general_notes">Observações Gerais</label>
                <textarea name="general_notes" id="general_notes" rows="3" class="wysiwyg form-control @error('general_notes') is-invalid @enderror">{{ old('general_notes', $chart->general_notes) }}</textarea>
                            @error('general_notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
let condIndex = {{ $chart->conditions->count() }};
document.getElementById('add-condition').addEventListener('click', function() {
    const container = document.getElementById('conditions-container');
    const template = container.querySelector('.condition-row').cloneNode(true);
    template.querySelectorAll('input, select').forEach(function(el) {
        el.name = el.name.replace(/conditions\[\d+\]/, 'conditions[' + condIndex + ']');
        if (el.type !== 'hidden') el.value = '';
    });
    container.appendChild(template);
    condIndex++;
});
</script>
@endpush
