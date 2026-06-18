@extends('layouts.adminlte', ['title' => 'Novo Odontograma'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Odontograma</h3>
        <div class="card-tools">
            <a href="{{ route('dental-charts.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('dental-charts.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Paciente *</label>
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
                        <label for="examination_date">Data do Exame *</label>
                        <input type="date" name="examination_date" id="examination_date" class="form-control @error('examination_date') is-invalid @enderror" value="{{ old('examination_date', date('Y-m-d')) }}" required>
                        @error('examination_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="procedure_type">Tipo de Procedimento *</label>
                        <select name="procedure_type" id="procedure_type" class="form-control @error('procedure_type') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['cleaning' => 'Limpeza', 'extraction' => 'Extração', 'surgery' => 'Cirurgia', 'exam' => 'Exame', 'other' => 'Outro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('procedure_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('procedure_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tartar_index">Índice de Tártaro</label>
                        <select name="tartar_index" id="tartar_index" class="form-control">
                            <option value="">Selecione</option>
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}" {{ old('tartar_index') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                <option value="{{ $i }}" {{ old('gingivitis_index') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" name="halitosis" id="halitosis" class="custom-control-input" value="1" {{ old('halitosis') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="halitosis">Halitose (Mau Hálito)</label>
                </div>
            </div>

            <hr>
            <h5>Condições Dentárias</h5>
            <div id="conditions-container">
                <div class="condition-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quadrante *</label>
                                <select name="conditions[0][quadrant]" class="form-control" required>
                                    <option value="">Q</option>
                                    @foreach(['1', '2', '3', '4'] as $q)
                                        <option value="{{ $q }}">{{ $q }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nº do Dente *</label>
                                <select name="conditions[0][tooth_number]" class="form-control" required>
                                    <option value="">Dente</option>
                                    @foreach([101,102,103,104,105,106,107,108,109,110,201,202,203,204,205,206,207,208,209,210,301,302,303,304,305,306,307,308,309,310,401,402,403,404,405,406,407,408,409,410] as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Condição *</label>
                                <select name="conditions[0][condition]" class="form-control" required>
                                    <option value="">Selecione</option>
                                    @foreach(['Placa', 'Tártaro', 'Gengivite', 'Retração Gengival', 'Mobilidade', 'Fratura', 'Lesão de Reabsorção', 'Pulpite', 'Cárie', 'Odontólito', 'Persistência', 'Anomalia', 'Outros'] as $cond)
                                        <option value="{{ $cond }}">{{ $cond }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Severidade</label>
                                <select name="conditions[0][severity]" class="form-control">
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }}/5</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Observações</label>
                                <input type="text" name="conditions[0][notes]" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.condition-row').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-condition" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus"></i> Adicionar Condição
            </button>

            <div class="form-group">
                <label for="general_notes">Observações Gerais</label>
                <textarea name="general_notes" id="general_notes" rows="3" class="wysiwyg form-control @error('general_notes') is-invalid @enderror">{{ old('general_notes') }}</textarea>
                            @error('general_notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Odontograma
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let condIndex = 1;
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
