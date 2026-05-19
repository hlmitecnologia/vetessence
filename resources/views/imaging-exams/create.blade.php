@extends('layouts.adminlte', ['title' => 'Novo Exame de Imagem'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Exame de Imagem</h3>
        <div class="card-tools">
            <a href="{{ route('imaging-exams.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('imaging-exams.store') }}" method="POST" enctype="multipart/form-data">
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
                        <label for="exam_type">Tipo de Exame *</label>
                        <select name="exam_type" id="exam_type" class="form-control @error('exam_type') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['xray' => 'Raio-X', 'ultrasound' => 'Ultrassom', 'ct' => 'Tomografia', 'mri' => 'Ressonância', 'ecg' => 'Eletrocardiograma', 'endoscopy' => 'Endoscopia', 'other' => 'Outro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('exam_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('exam_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="region">Região</label>
                        <input type="text" name="region" id="region" class="form-control" value="{{ old('region') }}" placeholder="Ex: Abdômen, Tórax">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exam_date">Data do Exame *</label>
                        <input type="date" name="exam_date" id="exam_date" class="form-control @error('exam_date') is-invalid @enderror" value="{{ old('exam_date', date('Y-m-d')) }}" required>
                        @error('exam_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vet_id">Veterinário Responsável *</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="radiologist_id">Radiologista</label>
                        <x-tom-select name="radiologist_id" id="radiologist_id" :value="old('radiologist_id')">
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('radiologist_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['scheduled' => 'Agendado', 'performed' => 'Realizado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="images">Imagens</label>
                        <input type="file" name="images[]" id="images" class="form-control-file" multiple accept="image/*">
                        <small class="text-muted">Formatos aceitos: JPG, PNG, DICOM</small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="findings">Achados</label>
                <textarea name="findings" id="findings" rows="4" class="form-control">{{ old('findings') }}</textarea>
            </div>
            <div class="form-group">
                <label for="impression">Impressão</label>
                <textarea name="impression" id="impression" rows="3" class="form-control">{{ old('impression') }}</textarea>
            </div>
            <div class="form-group">
                <label for="recommendations">Recomendações</label>
                <textarea name="recommendations" id="recommendations" rows="3" class="form-control">{{ old('recommendations') }}</textarea>
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
